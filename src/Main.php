<?php
namespace Aigisu;

use Aigisu\Alert\Alert;
use Aigisu\View\View;
use Slim\App as Slim;
use Slim\Container;

class Main extends Slim
{
    private $debug = false;

    public function __construct($items = [])
    {
        parent::__construct(new Configuration($items));
    }

    public function isDebug()
    {
        return $this->debug;
    }

    public function debug(bool $state = true)
    {
        $this->debug = $state;
        if ($state) {
            $this->runDebug();
        }
    }

    private function runDebug()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $this->getContainer()->get('settings')->replace(['displayErrorDetails' => true]);
        $this->getContainer()->get('settings')->replace(['addContentLengthHeader' => false]);
    }

    public function bootstrap()
    {
        $this->setRoutes();
        $this->setDatabase();
        $this->createSessions();
        $this->setView();
        $this->addControllerClasses();
    }

    private function setRoutes()
    {
        /** @noinspection PhpIncludeInspection */
        require_once Configuration::DIR_CONFIG . 'routes.php';
    }

    private function setDatabase()
    {
        /** @var $settings Configuration */
        $settings = $this->getContainer();
        $connection = new Connection($settings->database);
        $connection->setValidator($settings->locale, Configuration::DIR_CONFIG . 'lang');
        $connection->setAsGlobal();
        $connection->bootEloquent();
        $this->getContainer()['connection'] = $connection->connection();
    }

    private function createSessions()
    {
        if (session_status() == PHP_SESSION_NONE && !session_id()) {
            session_start();
        }

        $alert = new Alert();
        $alert->init();
    }

    private function setView()
    {
        $this->getContainer()['view'] = new View(Configuration::DIR_VIEW);
    }

    private function addControllerClasses()
    {
        /** @var $settings Configuration */
        $settings = $this->getContainer();

        foreach ($settings->controllers as $name) {
            $container[$name] = function (Container $container) use ($name) {
                return new $name($container);
            };
        }
    }
}