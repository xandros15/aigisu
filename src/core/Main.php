<?php
namespace Aigisu\Core;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Slim\App as Slim;

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
        $this->getContainer()['isDebug'] = function () use ($state) {
            return $state;
        };
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
        $database = new Database($settings->database);
        $database->setAsGlobal();
        $database->setEventDispatcher(new Dispatcher(new Container()));
        $database->bootEloquent();
        $this->getContainer()['connection'] = $database->connection();
    }
}