<?php
$query = (object) [
        'get' => (object) $_GET,
        'post' => (object) $_POST,
        'files' => (object) $_FILES,
];

use Slim\App as Slim;
use Slim\Router;
use Slim\Http\Request;
use models\Oauth;
use app\alert\Alert;
use app\core\Configuration;
use app\slim\SlimConfig;
use RedBeanPHP\R;

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
        R::setup('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        R::debug(self::$app->web->debug);
        R::freeze();
    }

    private function createSessions()
    {
        $alert = new Alert();
        $alert->init();
        $oauth = new Oauth();
        $oauth->run();
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