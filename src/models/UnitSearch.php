<?php

namespace models;

use models\Unit;
use Illuminate\Database\Eloquent\Collection;

class UnitSearch extends Unit
{
    const MAX_UNITS_DISPLAY = 30;

    public function search(array $params)
    {
        /* @var $query Collection */
        $query = Unit::all();
        $query = $this->parseSearch($params, $query);
        $query = $this->parseSort($params, $query);
        $query = $this->parsePage($params, $query);
        return $query->all();
    }

    protected function parsePage(array $params, Collection $query)
    {
        if (!isset($params['page'])) {
            return $query->slice(0, self::MAX_UNITS_DISPLAY);
        }
        $page = max(($params['page'] - 1) * self::MAX_UNITS_DISPLAY, 0);
        return $query->slice($page, self::MAX_UNITS_DISPLAY);
    }

    protected function parseSort(array $params, Collection $query)
    {
        if (!isset($params['sort'])) {
            return $query->sortByDesc('id');
        }
        $sort = strtolower($params['sort']);
        $desc = false;
        if (strpos($params['sort'], '-') === 0) {
            $desc = true;
            $sort = ltrim($sort, '-');
        }
        if (!in_array($sort, Unit::getColumns())) {
            return $query->sortByDesc('id');
        }
        if (!in_array($sort, Unit::getRarities())) {
            $enum     = array_flip(Unit::getRarities());
            $callback = function ($unit) use ($enum) {
                return $enum[$unit['rarity']];
            };
            return($desc) ? $query->sortByDesc($callback) : $query->sortBy($callback);
        }
        return ($desc) ? $query->sortByDesc($sort) : $query->sortBy($sort);
    }

    protected function parseSearch(array $params, Collection $query)
    {
        if (!isset($params['q'])) {
            return $query;
        }

        $arguments = $this->parseSearchQuery($params);
        foreach ($arguments as $namespace => $value) {
            $query = $query->where($namespace, $value);
        }

        return $query;
    }

    private function parseSearchQuery(array $params)
    {
        $arguments = explode(' ', $params['q']);

        $newArguments = [];
        foreach ($arguments as $argument) {
            $namespace = 'name';
            if (preg_match('/^(.+):(.+)$/', $argument, $matches)) {
                list($string, $namespace, $argument) = $matches;
            }
            if ($argument == 'male') {
                $namespace = 'is_male';
                $argument  = 1;
            }
            if ($argument == 'dmm') {
                $namespace = 'is_only_dmm';
                $argument  = 1;
            }
            if ($argument == 'nutaku') {
                $namespace = 'is_only_dmm';
                $argument  = 0;
            }

            $newArguments[$namespace] = $argument;
        }
        return $newArguments;
    }
}