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
        foreach ($rules as $pattern => $controllers) {
            if (isset($controllers['methods'])) {
                $this->slim->map($controllers['methods'], $pattern, $controllers['action'])
                    ->setName($controllers['name']);
            } else {
                $this->slim->group($pattern,
                    function () use ($controllers) {
                        foreach ($controllers as $pattern => $controller) {
                            $this->map($controller['methods'], $pattern, $controller['action'])
                            ->setName($controller['name']);
                    }
                });
            }
        }
    }

    private function setRouter(Router $router)
    {
        $this->router = $router;
    }

    public function getSlim()
    {
        return $this->slim;
    }

    public function getRouter()
    {
        return $this->router;
    }
}