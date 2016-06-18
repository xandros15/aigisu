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

    /** @var array */
    private $options;

    abstract public function getName() : string;

    public function init()
    {
        $this->options['initialized'] = true;
    }

    public function __construct()
    {
        $this->options = [
            'initialized' => false
        ];
        $this->init();
    }

    public function getCallbacks()
    {
        return $this->callbacks;
    }

    protected function addCallback(array $callbacks)
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