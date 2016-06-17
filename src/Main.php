<?php
namespace Aigisu;

use app\core\View;
use Slim\App as Slim;
use Slim\Container;
use Slim\Router;
use Slim\Http\Request;
use app\alert\Alert;
use app\core\Configuration;
use app\slim\SlimConfig;
use app\core\Connection;

class Main extends Slim
{
    /** @var Main */
    static $app;

    /** @var Configuration */
    public $web;

    /** @var Connection */
    public $connection;

    /** @var Slim */
    private $slim;

    public function __construct()
    {
        echo '<pre>';
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $this->configuration();
        parent::__construct($this->createContainer());
        $this->setRoutes($this->web->get('slim')['rules']);
        $this->bootstrap();
    }

    public function bootstrap()
    {
        static::$app = $this;
        $this->dbconnect();
        $this->createSessions();
        $this->createView();
        $this->addControllerClasses();
        dump($this);
        die();
    }

    private function configuration()
    {
        $this->web = Configuration::getInstance();
    }

    private function createContainer() : Container
    {
        $options = [];
        $options['settings'] = [
            'displayErrorDetails' => $this->web->get('debug')
        ];

        return new Container($options);
    }

    private function addControllerClasses()
    {
        $container = $this->getContainer();
        $controllers = $this->web->get('slim')['controllers'];

        foreach ($controllers as $name) {
            $container[$name] = function (Container $container) use ($name) {
                return new $name($container);
            };
        }
    }

    private function createView()
    {
        $container = $this->getContainer();
        $container['view'] = function (Container $container) {
            $router = $container->router;
            $callbacks = [];
            $callbacks['pathFor'] = function ($name, $data = [], $queryParams = []) use ($router) {
                return $router->pathFor($name, $data, $queryParams);
            };
            return new View(VIEW_DIR, $callbacks);
        };


    }

    private function dbconnect()
    {
        $this->connection = new Connection([
            'driver' => 'mysql',
            'host' => DB_HOST,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
        ]);
    }

    private function createSessions()
    {
        if (session_status() == PHP_SESSION_NONE && !session_id()) {
            session_start();
        }

        $alert = new Alert();
        $alert->init();
    }

    private function setRoutes(array $routes)
    {
        foreach ($routes as $route) {
            if (isset($route['group'])) {
                $callback = function () use ($route) {
                    foreach ($route['group'] as $singleRoute) {
                        $this->map($singleRoute['methods'],
                            $singleRoute['pattern'], $singleRoute['action']
                        )->setName($singleRoute['name']);
                    }
                };
                $this->group($route['pattern'], $callback);
            } else {
                $this->map($route['methods'], $route['pattern'], $route['action'])
                    ->setName($route['name']);
            }
        }
    }

    private function setSlim()
    {
        $config = new SlimConfig($this->web->slim);

        $this->slim = $config->getSlim();
        $this->router = $config->getRouter();
    }
}