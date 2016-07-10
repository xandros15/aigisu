<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-10
 * Time: 04:05
 */

namespace Aigisu;

use InvalidArgumentException;
use Slim\Http\Request;
use Slim\Router;


abstract class Sort
{
    const PARAM = 'sort';
    const SEPARATOR = ',';
    const SORT_DESC = 'desc';
    const SORT_ASC = 'asc';

    /** @var string */
    private $route;
    /** @var array */
    private $attributes;
    /** @var array */
    private $orders = [];
    /** @var array */
    private $query = [];
    /** @var Router */
    private $router;

    public function __construct(Request $request, Router $router)
    {
        $this->setQuery($request);
        $this->setOrders($request);
        $this->router = $router;
        $this->route = $request->getAttribute('route')->getName();
        $this->attributes = $request->getAttributes();
    }

    public function items() : array
    {
        return array_keys($this->columns());
    }

    public function getOrders() : array
    {
        return $this->orders;
    }

    public function pathFor(string $name) : string
    {
        $this->query[self::PARAM] = $this->getSortColumn($name);
        return $this->router->pathFor($this->route, $this->attributes, $this->query);
    }

    public function label(string $name) : string
    {
        $column = $this->getColumn($name);
        return $column['label'] ?? ucfirst($name);
    }

    abstract protected function columns() : array;

    protected function getDefaultOrders() : array
    {
        return [
            'id' => self::SORT_DESC
        ];
    }

    private function setQuery(Request $request)
    {
        $query = $request->getQueryParams();
        if (isset($query[self::PARAM])) {
            unset($query[self::PARAM]);
        }
        $this->query = $query;
    }

    private function setOrders(Request $request)
    {
        $param = $request->getParam(self::PARAM, '');
        $sort = ($param) ? explode(self::SEPARATOR, $param) : [];
        foreach ($sort as $item) {
            $column = trim($item, '-');
            if ($this->hasColumn($column)) {
                $this->orders[$column] = (!($item[0] <=> '-')) ? self::SORT_DESC : self::SORT_ASC;
            }
        }
        if (!$this->orders) {
            $this->orders = $this->getDefaultOrders();
        }
    }

    private function hasColumn(string $name) : bool
    {
        $columns = $this->columns();
        return isset($columns[$name]);
    }

    private function getSortColumn($name) : string
    {
        $directions = $this->orders;

        if (isset($directions[$name])) {
            $direction = $directions[$name] == self::SORT_ASC ? self::SORT_DESC : self::SORT_ASC;
            unset($directions[$name]);
        } else {
            $column = $this->getColumn($name);
            $direction = $column['default'] ?? self::SORT_ASC;
        }
        $directions = array_merge([$name => $direction], $directions);

        $sorts = [];
        foreach ($directions as $attribute => $direction) {
            $sorts[] = $direction === self::SORT_DESC ? '-' . $attribute : $attribute;
        }

        return implode(self::SEPARATOR, $sorts);
    }

    private function getColumn(string $name)
    {
        if (!$this->hasColumn($name)) {
            throw new InvalidArgumentException("Unknown column: {$name}");
        }

        $columns = $this->columns();
        return $columns[$name];
    }
}