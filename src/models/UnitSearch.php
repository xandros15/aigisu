<?php

namespace models;

use models\Unit;
use Illuminate\Database\Eloquent\Collection;

class UnitSearch extends Unit
{
    const MAX_UNITS_DISPLAY = 30;

    public $maxPages = 0;

    public function search(array $params)
    {
        /* @var $collection Collection */
        $collection = Unit::all();

        $collection = $this->parseSearch($params, $collection);
        $collection = $this->parseSort($params, $collection);

        $this->setMaxPages($collection);

        return $this->parsePage($params, $collection);
    }

    protected function parsePage(array $params, Collection $collection)
    {
        if (!isset($params['page'])) {
            return $collection->slice(0, self::MAX_UNITS_DISPLAY);
        }
        $page = max(($params['page'] - 1) * self::MAX_UNITS_DISPLAY, 0);

        $this->setMaxPages($collection);
        return $collection->slice($page, self::MAX_UNITS_DISPLAY);
    }

    protected function parseSort(array $params, Collection $collection)
    {
        if (!isset($params['sort'])) {
            return $collection->sortByDesc('id');
        }
        $sort = strtolower($params['sort']);
        $desc = false;
        if (strpos($params['sort'], '-') === 0) {
            $desc = true;
            $sort = ltrim($sort, '-');
        }
        if (!in_array($sort, Unit::getColumns())) {
            return $collection->sortByDesc('id');
        }
        if (!in_array($sort, Unit::getRarities())) {
            $enum     = array_flip(Unit::getRarities());
            $callback = function ($unit) use ($enum) {
                return $enum[$unit['rarity']];
            };
            return($desc) ? $collection->sortByDesc($callback) : $collection->sortBy($callback);
        }
        return ($desc) ? $collection->sortByDesc($sort) : $collection->sortBy($sort);
    }

    protected function parseSearch(array $params, Collection $collection)
    {
        if (!isset($params['q'])) {
            return $collection;
        }

        $arguments = $this->parseSearchQuery($params);
        foreach ($arguments as $namespace => $value) {
            $collection = $collection->where($namespace, $value);
        }

        return $collection;
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

    private function setMaxPages(Collection $collection)
    {
        $this->maxPages = (int) ceil($collection->count() / self::MAX_UNITS_DISPLAY);
    }
}