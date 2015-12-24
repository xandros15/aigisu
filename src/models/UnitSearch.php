<?php

namespace models;

use models\Unit;
use stdClass;
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

        return $query->get();
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
        foreach ($arguments as $argument) {
            $query = $query->where($argument->column, $argument->operator, $argument->value);
        }

        return $query;
    }

    private function parseSearchQuery(array $params)
    {
        $arguments = explode(' ', $params['q']);


        $newArguments = [];
        foreach ($arguments as $value) {
            $column   = 'name';
            $operator = '=';
            $equal    = true;
            if (strpos($value, '-') === 0) {
                $value = ltrim($value, '-');
                $equal = false;
            }

            if (strpos($value, ':') !== false) {
                list($column, $value) = explode(':', $value, 2);
            }
            if (!$equal) {
                $operator = '!=';
            }
            if (strpos($value, '*') !== false) {
                $operator = ($equal) ? 'LIKE' : 'LIKE NOT';
                $value    = str_replace('*', '%', $value);
                while (strpos($value, '%%') !== false) {
                    $value = str_replace('%%', '%', $value);
                }
            } else {
                switch ($value) {
                    case 'male':
                        $column = 'is_male';
                        $value  = 1;
                        break;
                    case 'dmm':
                        $column = 'is_only_dmm';
                        $value  =  1;
                        break;
                    case 'nutaku':
                        $column = 'is_only_dmm';
                        $value  = 0;
                        break;
                }
            }

            $param = new stdClass();

            $param->column   = $column;
            $param->operator = $operator;
            $param->value    = $value;

            $newArguments[] = $param;
        }

        return $newArguments;
    }
}