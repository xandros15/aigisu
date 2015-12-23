<?php

namespace models;

use models\Unit;
use Illuminate\Database\Eloquent\Builder as Query;

class UnitSearch extends Unit
{
    const UNITS_PER_PAGE = 30;

    public $maxPages = 0;

    public function search(array $params)
    {
        $query = Unit::with('images');

        $query = $this->parseSearch($params, $query);
        $query = $this->parseSort($params, $query);
        $query = $this->parsePagination($params, $query);

        return  $query->get();
    }

    protected function parsePagination(array $params, Query $query)
    {
        $page = (isset($params['page'])) ? $params['page'] : 1;

        $this->maxPages = (int) ceil($query->toBase()->getCountForPagination() / self::UNITS_PER_PAGE);

        return $query->forPage($page, self::UNITS_PER_PAGE);
    }

    protected function parseSort(array $params, Query $query)
    {
        if (!isset($params['sort'])) {
            return $query->orderBy('id', 'desc');
        }

        $column    = strtolower($params['sort']);
        $direction = 'asc';

        if (strpos($params['sort'], '-') === 0) {
            $direction = 'desc';
            $column    = ltrim($column, '-');
        }

        if (!in_array($column, Unit::getColumns())) {
            return $query->orderBy('id', 'desc');
        }

        return $query->orderBy($column, $direction);
    }

    protected function parseSearch(array $params, Query $query)
    {
        if (empty($params['q'])) {
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
}