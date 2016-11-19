<?php
namespace Aigisu\Core;

use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Slim\App as Slim;

class Main extends Slim
{

    /**
     * Main constructor.
     * @param array $items
     */
    public function __construct($items = [])
    {
        parent::__construct(new Configuration($items));
    }

    /**
     * @param bool $state
     */
    public function debug(bool $state = true)
    {
        defined('__DEBUG__') || define('__DEBUG__', $state);
        if ($this->getContainer()->get('isDebug')) {
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