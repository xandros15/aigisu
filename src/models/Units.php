<?php

namespace models;

use RedBeanPHP\R;
use RedBeanPHP\RedException;

class Units
{
    const MAX_UNITS_DISPLAY = 30;

    public $maxUnits  = 0;
    protected $sort   = '';
    protected $search = [];

    public static function tableName()
    {
        return 'units';
    }

    public static function getRarities()
    {
        return ['black', 'sapphire', 'platinum', 'gold', 'silver', 'bronze', 'iron'];
    }

    public static function getColumnNames()
    {
        return ['id', 'name', 'orginal', 'icon', 'link', 'linkgc', 'rarity', 'is_male', 'is_only_dmm'];
    }

    public static function load()
    {
        $model = new Units();
        $model->setSort();
        $model->setSearch();
        return $model;
    }

    public static function getCurrentPage()
    {
        global $query;
        return (isset($query->get->page) && preg_match('/^\d+$/', $query->get->page)) ? $query->get->page : 1;
    }

    public function getMaxPages()
    {
        return (int) ceil(max($this->maxUnits, 1) / self::MAX_UNITS_DISPLAY);
    }

    public function getMaxUnits()
    {
        return $this->maxUnits;
    }

    public function getAllUnits()
    {
        $start = (int) self::MAX_UNITS_DISPLAY * max($this->getCurrentPage() - 1, 0);
        $limit = (int) self::MAX_UNITS_DISPLAY;

        try {
            if ($this->search) {
                $results = R::findLike(self::tableName(), $this->searchUnits($this->search), $this->sort);
                $this->setMaxUnits($results);
            } else {
                $results = R::findAll(self::tableName(), $this->sort);
                $this->setMaxUnits($results);
            }
        } catch (RedException $exc) {
            var_dump($exc->getMessage(), $exc->getTrace());
            $results = [];
        }
        return array_slice($results, $start, $limit);
    }

    protected function setSort()
    {
        global $query;

        $direction = 'DESC';
        $column    = 'id';
        if (isset($query->get->sort)) {
            $direction = 'ASC';
            $order     = strtolower($query->get->sort);
            if (strpos($order, '-') === 0) {
                $order     = substr($order, 1);
                $direction = 'DESC';
            }
            if (($orders = array_intersect(self::getColumnNames(), [$order]))) {
                $column = reset($orders);
            }
        }

        $this->sort = sprintf(' ORDER BY %s.`%s` %s', self::tableName(), $column, $direction);
    }

    protected function setSearch()
    {
        global $query;
        $this->search = (!empty($query->get->q)) ? $this->parseSearch($query->get->q) : [];
    }

    protected function setMaxUnits(array $units)
    {
        $this->maxUnits = count($units);
    }

    private function searchUnits(array $query)
    {
        foreach ($query as $type => $value) {
            if (!in_array($type, Units::getColumnNames()) || !is_array($value)) {
                unset($query[$type]);
            }
        }
        return $query;
    }

    protected function parseSearch($search)
    {
        $arguments    = explode(' ', $search);
        $newArguments = [];
        foreach ($arguments as $argument) {
            $namespace = 'name';
            if (preg_match('/^(.+):(.+)$/', $argument, $matches)) {
                $namespace = $matches[1];
                $argument  = $matches[2];
            }
            if($argument == 'male'){
                $namespace = 'is_male';
                $argument = 1;
            }
            if($argument == 'dmm'){
                $namespace = 'is_only_dmm';
                $argument = 1;
            }
            if($argument == 'nutaku'){
                $namespace = 'is_only_dmm';
                $argument = 0;
            }
            if (strpos($argument, '*') === 0) {
                $argument = substr_replace($argument, '%', 0, 1);
            }
            if (strrpos($argument, '*') === strlen($argument) - 1) {
                $argument = substr_replace($argument, '%', -1, 1);
            }
            $newArguments[$namespace][] = $argument;
        }
        return $newArguments;
    }

    public static function editUnit()
    {
        R::debug(false);
        global $query;
        $unitPost = (object) $query->post->unit;
        if (isset($unitPost->id) && preg_match('/^\d+$/', $unitPost->id)) {
            $response = ['valid' => []];
            $unit     = R::load(TB_NAME, $unitPost->id);
            if (preg_match('/^[\w]+$/', $unitPost->name)) {
                $unit->name                    = $unitPost->name;
                $response['valid'][0]['value'] = true;
                $response['valid'][0]['name']  = 'unit-name';
            } else {
                $response['valid'][0]['value'] = false;
                $response['valid'][0]['name']  = 'unit-name';
            }
            if (!empty($unitPost->rarity) && in_array($unitPost->rarity, Units::getRarities())) {
                $unit->rarity                  = $unitPost->rarity;
                $response['valid'][2]['value'] = true;
                $response['valid'][2]['name']  = 'unit-rarity';
            } else {
                $response['valid'][2]['value'] = false;
                $response['valid'][2]['name']  = 'unit-rarity';
            }
            R::store($unit);
            die(json_encode($response));
        }
        $_POST['valid'] = false;
        die(json_encode($_POST));
    }
}