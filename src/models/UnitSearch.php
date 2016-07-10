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

        $this->parseSearch($params['search'], $query);
        $this->parseSort($params['order'], $query);
        $this->parsePagination($params['page'], $query);
        return $query;
    }

    protected function parseSearch(string $search, Query $query)
    {
        if (!$search) {
            return;
        }

        $arguments = $this->parseSearchQuery($search);

        foreach ($arguments as $argument) {
            if ($argument->column == 'tags') {
                $this->searchWithTags($query, $argument);
            } else {
                $query->where($argument->column, $argument->operator, $argument->value);
            }
        }
    }

    private function parseSearchQuery(string $search)
    {
        $arguments = explode(' ', trim($search));
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

    protected function parseSort(array $orders, Query $query)
    {
        if (!$orders) {
            $query->orderBy('id', 'desc');
            return;
        }
        foreach ($orders as $column => $order) {
            if (in_array($column, self::getColumns())) {
                $query->orderBy($column, $order);
            }
        }
    }

    protected function parsePagination(int $page, Query $query)
    {
        $query->forPage($page, self::UNITS_PER_PAGE);
        $this->count = $query->toBase()->getCountForPagination();
    }
}