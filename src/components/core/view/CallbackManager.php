<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-08
 * Time: 18:28
 */

namespace Aigisu\view;


class CallbackManager
{
    private $callbacks = [];

    public function __call($name, $arguments)
    {
        if (!isset($this->callbacks[$name])) {
            throw new \BadMethodCallException();
        }
        return call_user_func_array($this->callbacks[$name], $arguments);
    }

    public function addCallbacks(array $callbacks)
    {
        foreach ($callbacks as $name => $callback) {
            $this->addCallback($name, $callback);
        }
    }

    public function addCallback(string $name, callable $callback)
    {
        $this->callbacks[$name] = $callback;
    }

    public function addClassCallbacks(ViewExtension $extension)
    {
        $extension->applyCallbacks($this);
    }
}