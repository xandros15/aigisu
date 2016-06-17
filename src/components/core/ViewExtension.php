<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-06-18
 * Time: 00:43
 */

namespace app\core;


use Slim\Container;

class ViewExtension
{
    private $callbacks = [];

    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'base-extension';
    }

    public function __get($name)
    {
        if ($this->container->has($name)) {
            return $this->container->get($name);
        }
        throw new \InvalidArgumentException();
    }

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
            $this->callbacks[$name] = $callback;
        }
    }
}