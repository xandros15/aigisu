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
            if ($this->hasParam($column)) {
                $this->orders[$column] = (!($item[0] <=> '-')) ? self::SORT_DESC : self::SORT_ASC;
            }
        }
        if (!$this->orders) {
            $this->orders = $this->getDefaultOrders();
        }
    }

    protected function hasParam(string $name) : bool
    {
        $params = $this->getParams();
        return isset($params[$name]);
    }

    abstract protected function getParams() : array;

    protected function getDefaultOrders() : array
    {
        return [
            'id' => self::SORT_DESC
        ];
    }

    public function items() : array
    {
        return array_keys($this->getParams());
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function pathFor(string $name) : string
    {
        $this->query[self::PARAM] = $this->getSortParam($name);
        return $this->router->pathFor($this->route, $this->attributes, $this->query);
    }

    protected function getSortParam($name) : string
    {
        if (!$this->hasParam($name)) {
            throw new InvalidArgumentException("Unknown param: $name}");
        }

        $directions = $this->orders;

        if (isset($directions[$name])) {
            $direction = $directions[$name] == self::SORT_ASC ? self::SORT_DESC : self::SORT_ASC;
            unset($directions[$name]);
        } else {
            $param = $this->getParam($name);
            $direction = $param['default'] ?? self::SORT_ASC;
        }
        $directions = array_merge([$name => $direction], $directions);

        $sorts = [];
        foreach ($directions as $attribute => $direction) {
            $sorts[] = $direction === self::SORT_DESC ? '-' . $attribute : $attribute;
        }

        return implode(self::SEPARATOR, $sorts);
    }

    protected function getParam(string $name, $default = [])
    {
        $params = $this->getParams();
        return ($this->hasParam($name)) ? $params[$name] : $default;
    }

    public function label(string $name) : string
    {
        if (!$this->hasParam($name)) {
            throw new InvalidArgumentException("Unknown param: $name}");
        }

        $param = $this->getParam($name);
        return $param['label'] ?? ucfirst($name);
    }
}