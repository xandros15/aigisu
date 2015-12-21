<?php

namespace models;

use models\Unit;
use Illuminate\Database\Query\Builder as Query;

class UnitSearch extends Unit
{
    const MAX_UNITS_DISPLAY = 30;

    public $maxPages = 0;

    public function search(array $params)
    {
        /* @var $query Query */
        $query = Unit::query()->toBase();

        $query = $this->parseSearch($params, $query);
        $query = $this->parseSort($params, $query);

        return $this->parsePage($params, $query)->get();
    }

    protected function parsePage(array $params, Query $query)
    {
        $this->setMaxPages($query);
        $parsed = $query->limit(self::MAX_UNITS_DISPLAY);
        if (!isset($params['page'])) {
            return $parsed;
        }
        $page = max(($params['page'] - 1) * self::MAX_UNITS_DISPLAY, 0);

        return $query->offset($page);
    }

    protected function parseSort(array $params, Query $query)
    {
        if (!isset($params['sort'])) {
            return $query->orderBy('id', 'desc');
        }

        $column  = strtolower($params['sort']);
        $direction = 'asc';

        if (strpos($params['sort'], '-') === 0) {
            $direction = 'desc';
            $column  = ltrim($column, '-');
        }

        if (!in_array($column, Unit::getColumns())) {
            return $query->orderBy('id', 'desc');
        }

        return $query->orderBy($column, $direction);
    }

    protected function parseSearch(array $params, Query $query)
    {
        if (!isset($params['q'])) {
            return $query;
        }

        $arguments = $this->parseSearchQuery($params);
        foreach ($arguments as $namespace => $value) {
            $query = $query->where($namespace, '=', $value);
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

    private function setMaxPages(Query $query)
    {
        $this->maxPages = (int) ceil($query->count() / self::MAX_UNITS_DISPLAY);
    }
}