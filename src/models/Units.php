<?php

namespace models;

use RedBeanPHP\R;
use RedBeanPHP\RedException;
use app\alert\Alert;

class Units
{
    const MAX_UNITS_DISPLAY = 30;

    public $maxUnits  = 0;
    public $units     = [];
    public $images    = [];
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
        return ['id', 'name', 'original', 'icon', 'link', 'linkgc', 'rarity', 'is_male', 'is_only_dmm'];
    }

    public static function getCurrentPage()
    {
        global $query;
        return (isset($query->get->page) && preg_match('/^\d+$/', $query->get->page)) ? $query->get->page : 1;
    }

    public static function load()
    {
        $model = new Units();
        $model->setSort();
        $model->setSearch();
        $model->findAll();
        return $model;
    }

    public function findAll()
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
            $results = array_slice($results, $start, $limit, true);
            $this->setImages(array_keys($results));
        } catch (RedException $exc) {
            var_dump($exc->getMessage(), $exc->getTrace());
            $results = [];
        }
        return $this->units = $results;
    }

    public static function loadOne($id)
    {
        $model = new Units();
        $model->findOne($id);
        return $model;
    }

    public function findOne($id)
    {
        if (isset($this->units[$id])) {
            return $this->units[$id];
        }
        try {
            $result = R::load(self::tableName(), $id);
            if ($result) {
                $this->setImages([$result->id]);
                $this->units[$result->id] = $result;
            }
        } catch (RedException $exc) {
            var_dump($exc->getMessage(), $exc->getTrace());
        }
        $this->setMaxUnits($this->units);
        return $this->units[$id];
    }

    public function getMaxPages()
    {
        return (int) ceil(max($this->maxUnits, 1) / self::MAX_UNITS_DISPLAY);
    }

    public function getMaxUnits()
    {
        return $this->maxUnits;
    }

    public function getUnitById($id)
    {
        return isset($this->units[$id]) ? $this->units[$id] : null;
    }

    public function getUnits()
    {
        return $this->units;
    }

    public function getUnitImages($unitId)
    {
        return isset($this->images[$unitId]) ? $this->images[$unitId] : [];
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

    protected function setImages(array $unitsId)
    {
        $images       = R::findLike(Images::tableName(), ['units_id' => $unitsId]);
        $newImageList = [];
        foreach ($images as $image) {
            $newImageList[$image->units_id][] = $image;
        }
        $this->images = $newImageList;
    }

    protected function searchUnits(array $query)
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

    public static function editUnit($post)
    {
        $unitPost = (object) $post->unit;
        if (($errors   = self::validate($unitPost))) {
            foreach ($errors as $error) {
                Alert::add($error, Alert::ERROR);
            }
            return false;
        }
        R::freeze();
        $unit = R::load(self::tableName(), $unitPost->id);
        if ($unit->isEmpty()) {
            return Alert::add("Unit not found", Alert::ERROR);
        }
        $unit->name      = $unitPost->name;
        $unit->rarity    = $unitPost->rarity;
        $unit->isOnlyDmm = (bool) isset($unitPost->isOnlyDmm) && $unitPost->isOnlyDmm;
        $unit->isMale    = (bool) isset($unitPost->isMale) && $unitPost->isMale;
        R::store($unit);
        Alert::add("Unit {$unit->original} successful updated.");
    }

    public static function validate($unit)
    {
        $errors = [];

        if (!preg_match('/^[\w]+$/', $unit->name)) {
            $errors[] = "Wrong name for unit.";
        }
        if (!in_array($unit->rarity, Units::getRarities())) {
            $errors[] = "Wrong rarity name for unit.";
        }

        return $errors;
    }
}