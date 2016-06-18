<?php
namespace app\core\View;

use Slim\Container;
use Slim\Interfaces\RouterInterface;

/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-06-18
 * Time: 03:11
 */

/** @property RouterInterface $router */
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
        parent::init();
        $this->addCallback('pathFor', [$this, 'pathFor']);
        $this->addCallback('siteUrl', [$this, 'getSiteUrl']);
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

    public function getSiteUrl()
    {
        return $this->config['siteUrl'];
    }

    public function getName() : string
    {
        return __CLASS__;
    }
}