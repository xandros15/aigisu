<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-09
 * Time: 03:39
 */

namespace Models;

use Slim\Http\Request;
use Slim\Router;

class UnitSort
{
    const PARAM = 'sort';
    protected $items = [
        'name' => 'Name',
        'original' => 'Original name',
        'rarity' => 'Rarity'
    ];

    private $routeName;
    private $attributes;
    private $sort;
    private $query;
    private $router;

    public function __construct(Request $request, Router $router)
    {
        $this->router = $router;
        $this->sort = $request->getQueryParam('sort', '');
        $this->query = $request->getQueryParams();
        $this->routeName = $request->getAttribute('route')->getName();
        $this->attributes = $request->getAttributes();
    }

    public function pathFor($name) : string
    {
        if (!isset($this->items[$name])) {
            return '#';
        }
        $newQuery = [self::PARAM => ($name == $this->sort) ? '-' . $name : $name];
        $mergedQuery = ($this->query) ? array_merge($this->query, $newQuery) : $newQuery;
        return $this->router->pathFor($this->routeName, $this->attributes, $mergedQuery);
    }

    public function items()
    {
        return $this->items;
    }
}