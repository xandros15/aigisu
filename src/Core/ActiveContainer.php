<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-07-09
 * Time: 19:05
 */

namespace Aigisu\Core;


use Interop\Container\ContainerInterface;

abstract class ActiveContainer
{
    /** @var ContainerInterface */
    private $container;

    /**
     * ActiveContainer constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     * @param null $default
     *
     * @return mixed
     */
    protected function get(string $name, $default = null)
    {
        if (!$this->container->has($name)) {
            return $default;
        }

        return $this->container->get($name);
    }
}
