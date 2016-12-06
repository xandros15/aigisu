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

        if ($this->getContainer()->get('isDebug')) {
            error_reporting(E_ALL);
            $this->getContainer()->get('settings')->replace([
                'displayErrorDetails' => true,
                'addContentLengthHeader' => false
            ]);
        }
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