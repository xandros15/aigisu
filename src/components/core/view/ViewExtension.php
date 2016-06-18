<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-06-18
 * Time: 00:43
 */

namespace app\core\view;

abstract class ViewExtension
{
    private $callbacks = [];

    abstract public function getName() : string;

    public function getCallbacks()
    {
        return $this->callbacks;
    }

    public function addCustomsCallback(array $callbacks)
    {
        foreach ($callbacks as $name => $callback) {
            if (!is_callable($callback)) {
                throw new \InvalidArgumentException("Method `{$name}` isn't callable");
            }

            if ($callback instanceof \Closure) {
               $callback = $callback->bindTo($this);
            }

            $this->callbacks[$name] = $callback;
        }
    }
}