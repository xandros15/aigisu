<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-11-14
 * Time: 20:31
 */

namespace Aigisu\Components;


use Interop\Container\ContainerInterface;

class Dispatcher
{
    /** @var array list of callbacks */
    private $list;
    /** @var  ContainerInterface */
    private $container;

    public function __construct(array $list, ContainerInterface $container)
    {
        list($this->list, $this->container) = [$list, $container];
    }

    public function call(string $name)
    {
        if (!isset($this->list[$name])) {
            throw new \InvalidArgumentException("{$name} callback doesn't exist");
        }

        return $this->list[$name]($this->container);
    }

}