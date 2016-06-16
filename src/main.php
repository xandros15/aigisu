<?php

use app\core\View;
use Slim\App as Slim;
use Slim\Container;
use Slim\Router;
use Slim\Http\Request;
use app\alert\Alert;
use app\core\Configuration;
use app\slim\SlimConfig;
use app\core\Connection;

class Main extends \Slim\App
{
    /** @var Main */
    static $app;

    /** @var Router */
    public $router;

    /** @var Request */
    public $request;

    /** @var Configuration */
    public $web;

    /** @var Connection */
    public $connection;

    /** @var Slim */
    private $slim;

    public function __construct()
    {
        $this->bootstrap();
        parent::__construct($this->createContainer());
    }

    public function bootstrap()
    {
        static::$app = $this;
        $this->setAutoloader();
        $this->configuration();
        $this->dbconnect();
        $this->createSessions();
        $this->setSlim();
    }

    private function configuration()
    {
        $this->web = Configuration::getInstance();
        $this->setRoutes($this->web->get('slim')['rules']);

    }

    private function createContainer() : Container
    {
        $options = [];
        $options['settings'] = [
            'displayErrorDetails' => $this->web->get('debug')
        ];

        return new Container($options);
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

    private function setAutoloader()
    {
        require_once ROOT_DIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    }

    private function setRoutes(array $routes)
    {
        foreach ($routes as $controllers) {
            if (isset($controllers['groups'])) {
                $this->group($controllers['pattern'], function () use ($controllers) {
                    foreach ($controllers['groups'] as $controller) {
                        $this->map($controller['methods'], $controller['pattern'], $controller['action'])
                            ->setName($controller['name']);
                    }
                });
            } else {
                $this->map($controllers['methods'], $controllers['pattern'], $controllers['action'])
                    ->setName($controllers['name']);
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