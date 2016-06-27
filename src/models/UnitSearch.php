<?php

namespace Models;

use Illuminate\Database\Eloquent\Builder as Query;
use stdClass;

class UnitSearch extends Unit
{
    const UNITS_PER_PAGE = 10;

    public $count = 0;

    public function search(array $params)
    {
        $query = Unit::with('images')->newQuery();

        $this->parseSearch($params, $query);
        $this->parseSort($params, $query);
        $this->parsePagination($params, $query);
        return $query;
    }

    protected function parseSearch(array $params, Query $query)
    {
        if (empty($params['q'])) {
            return $query;
        }

        $arguments = $this->parseSearchQuery($params);

        foreach ($arguments as $argument) {
            if ($argument->column == 'tags') {
                $this->searchWithTags($query, $argument);
            } else {
                $query->where($argument->column, $argument->operator, $argument->value);
            }
        }

        return $query;
    }

    private function parseSearchQuery(array $params)
    {
        $arguments = explode(' ', trim($params['q']));
        $newArguments = [];
        foreach ($arguments as $value) {

            $column = 'tags';
            $operator = '=';
            $equal = true;

            if (strpos($value, '-') === 0) {
                $value = ltrim($value, '-');
                $equal = false;
            }

            if (strpos($value, ':') !== false) {
                list($column, $value) = explode(':', $value, 2);
                if (!in_array($column, $this->fillable)) {
                    continue;
                }
            }

            if (!$equal) {
                $operator = '!=';
            }

            if (strpos($value, '*') !== false) {
                $operator = ($equal) ? 'LIKE' : 'LIKE NOT';
                $value = str_replace('*', '%', $value);
                while (strpos($value, '%%') !== false) {
                    $value = str_replace('%%', '%', $value);
                }
            }

            $newArguments[] = $this->parsedArgumentsToObject($column, $operator, $value);
        }

        return $newArguments;
    }

    private function parsedArgumentsToObject($column, $operator, $value)
    {
        $param = new stdClass();

        $param->operator = $operator;
        $param->value = $value;
        $param->column = $column;

        return $param;
    }

    private function searchWithTags(Query $query, stdClass $argument)
    {
        return $query->where(function (Query $query) use ($argument) {
            $query->where('unit.name', $argument->operator, $argument->value)->orWhereHas('tags',
                function (Query $query) use ($argument) {
                    $query->where('name', $argument->operator, $argument->value);
                });
        });
    }

    protected function parseSort(array $params, Query $query)
    {
        if (!isset($params['sort'])) {
            return $query->orderBy('id', 'desc');
        }

        $column = strtolower($params['sort']);
        $direction = 'asc';

        if (strpos($params['sort'], '-') === 0) {
            $direction = 'desc';
            $column = ltrim($column, '-');
        }

        if (!in_array($column, Unit::getColumns())) {
            return $query->orderBy('id', 'desc');
        }

        return $query->orderBy($column, $direction);
    }

    protected function parsePagination(array $params, Query $query)
    {
        $this->count = $query->toBase()->getCountForPagination();

        $page = (isset($params['page'])) ? $params['page'] : 1;
        return $query->forPage($page, self::UNITS_PER_PAGE);
    }
}