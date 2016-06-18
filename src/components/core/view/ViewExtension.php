<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-06-18
 * Time: 00:43
 */

namespace app\core\View;

abstract class ViewExtension
{
    /** @var array */
    private $callbacks = [];

    abstract public function getName() : string;

    abstract public function init();

    public function __construct()
    {
        $this->init();
    }

    public function getCallbacks()
    {
        return $this->callbacks;
    }

    protected function addCallback($name, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException("Method `{$name}` isn't callable");
        }

        if ($callback instanceof \Closure) {
            $callback = $callback->bindTo($this);
        }

        $this->callbacks[$name] = $callback;
    }
}