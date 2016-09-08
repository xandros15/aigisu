<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-08
 * Time: 01:33
 */

namespace Aigisu\Core;


use Interop\Container\ContainerInterface;

class MiddlewareProvider
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * MiddlewareProvider constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     * @return Middleware
     */
    public function createMiddleware(string $name)
    {
        if (!is_subclass_of($name, Middleware::class)) {
            throw new \InvalidArgumentException("Class {$name} doesn't exist or isn't middleware");
        }

        return new $name($this->container);
    }
}