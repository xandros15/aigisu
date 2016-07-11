<?php

namespace Models;

class UnitSearch extends Unit
{
    /** @var \Illuminate\Database\Eloquent\Builder */
    public $builder;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->newQuery();
    }

    public function newQuery()
    {
        if (!$this->builder) {
            $this->builder = parent::newQuery();
        }
        return $this->builder;
    }

    public function search(array $params)
    {
    }

    public function setSort(array $orders)
    {
        foreach ($orders as $column => $order) {
            $this->builder->orderBy($column, $order);
        }
    }

    public function getTotalItems() : int
    {
        return $this->builder->toBase()->getCountForPagination();
    }
}