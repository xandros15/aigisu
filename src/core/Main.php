<?php
namespace Aigisu\Core;

use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Slim\App as Slim;

class Main extends Slim
{
    /** @var bool */
    private $debug = false;

    /**
     * Main constructor.
     * @param array $items
     */
    public function __construct($items = [])
    {
        parent::__construct(new Configuration($items));
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param bool $state
     */
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
        $routerProvider = new RouteProvider($this);
        $routerProvider->map();
    }

    private function setDatabase()
    {
        /** @var $database CapsuleManager */
        $database = $this->getContainer()->get(CapsuleManager::class);
        $database->setAsGlobal();
        $database->bootEloquent();
    }
}