<?php
namespace app\core\View;

use Slim\Container;

/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-06-18
 * Time: 03:11
 */
class SlimViewExtension extends ViewExtension
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __get($name)
    {
        if ($this->container->has($name)) {
            return $this->container->get($name);
        }
        throw new \InvalidArgumentException();
    }

    public function getName() : string
    {
        return __CLASS__;
    }
}