<?php

use Slim\App as Slim;
use Slim\Router;
use Slim\Http\Request;
use app\alert\Alert;
use app\core\Configuration;
use app\slim\SlimConfig;
use app\core\Connection;

class Main
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

    public function bootstrap()
    {
        static::$app = $this;
        $this->setAutoloader();
        $this->configuration();
        $this->dbconnect();
        $this->createSessions();
        $this->setSlim();
    }

    public function run()
    {
        $this->slim->run();
    }

    private function configuration()
    {
        $this->web = Configuration::getInstance();
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

    private function setSlim()
    {
        $config = new SlimConfig($this->web->slim);

        $this->slim   = $config->getSlim();
        $this->router = $config->getRouter();
    }
}