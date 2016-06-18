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
        parent::__construct();
    }

    public function init()
    {
        $this->addCallback([$this, 'pathFor']);
    }

    public function __get($name)
    {
        if ($this->container->has($name)) {
            return $this->container->get($name);
        }
        throw new \InvalidArgumentException();
    }

    public function pathFor($name, $data = [], $queryParams = [])
    {
        return $this->router->pathFor($name, $data, $queryParams);
    }

    public function getName() : string
    {
        return __CLASS__;
    }
}