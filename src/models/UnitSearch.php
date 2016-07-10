<?php

namespace Models;

use Illuminate\Database\Eloquent\Builder;

class UnitSearch extends Builder
{

    public function __construct()
    {
        $unit = new Unit();
        parent::__construct($unit->query()->toBase());
        $this->setModel($unit);
    }

    public function search(array $params)
    {

    }

    public function setSort(array $orders)
    {
        foreach ($orders as $column => $order) {
            if (in_array($column, Unit::getColumns())) {
                $this->orderBy($column, $order);
            }
        }
    }

    public function getTotalItems() : int
    {
        return $this->toBase()->getCountForPagination();
    }
}