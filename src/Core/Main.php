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

    public function bootstrap()
    {
        !$this->getContainer()->get('isDebug') || $this->runSlimDebug();
        $this->setRoutes();
        $this->setDatabase();
    }

    public function runSlimDebug()
    {
        $this->getContainer()->get('settings')->replace([
            'displayErrorDetails' => true,
            'addContentLengthHeader' => false
        ]);
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
