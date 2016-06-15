<?php

namespace app\slim;

use Slim\App;
use Slim\Container;
use Slim\Router;

class SlimConfig
{
    /** @var Container */
    public $container;

    /** @var App */
    public $slim;

    /** @var Router */
    public $router;

    public function __construct(array $config)
    {
        $this->setContainer($config['values']);
        $this->setSlim($this->container);
        $this->setSlimRules($config['rules']);
        $this->setRouter($this->container->router);
    }

    public function getSlim()
    {
        return $this->slim;
    }

    public function getRouter()
    {
        return $this->router;
    }

    private function setRouter(Router $router)
    {
        $this->router = $router;
    }

    private function setContainer(array $values)
    {
        $this->container = new Container($values);
    }

    private function setSlim(Container $container)
    {
        $this->slim = new App($container);
    }

    private function setSlimRules($rules)
    {
        foreach ($rules as $controllers) {
            if (isset($controllers['groups'])) {
                $this->slim->group($controllers['pattern'], function () use ($controllers) {
                    foreach ($controllers['groups'] as $controller) {
                        $this->map($controller['methods'], $controller['pattern'], $controller['action'])
                            ->setName($controller['name']);
                    }
                });
            } else {
                $this->slim->map($controllers['methods'], $controllers['pattern'], $controllers['action'])
                    ->setName($controllers['name']);
            }
        }
    }
}