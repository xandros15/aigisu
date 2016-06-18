<?php
namespace Aigisu;

use app\alert\Alert;
use app\core\Configuration;
use app\core\Connection;
use app\core\View\SlimViewExtension;
use app\core\View\View;
use Slim\App as Slim;
use Slim\Container;

class Main extends Slim
{

    public function debug($state = true)
    {
        if ($state) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            $this->getContainer()->get('settings')->replace(['displayErrorDetails' => true]);
            $this->getContainer()->get('settings')->replace(['addContentLengthHeader' => false]);
        }
    }

    public function bootstrap()
    {
        $this->setRoutes();
        $this->setConfiguration();
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

    private function setConfiguration()
    {
        $this->getContainer()['config'] = function () {
            return new Configuration();
        };
    }

    private function setDatabase()
    {
        /** @var $config Configuration */
        $config = $this->getContainer()['config'];
        $connection = new Connection($config['database']);
        $connection->setValidator($config->get('locale', 'en'), Configuration::DIR_CONFIG . 'langs');
        $connection->setAsGlobal();
        $connection->bootEloquent();
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
        $this->getContainer()['view'] = function (Container $container) {
            $view = new View(Configuration::DIR_VIEW);
            $slimViewExtension = new SlimViewExtension($container);
            $view->addExtension($slimViewExtension);
            return $view;
        };
    }

    private function addControllerClasses()
    {
        $controllers = $this->getContainer()->get('config')->get('controllers');

        foreach ($controllers as $name) {
            $container[$name] = function (Container $container) use ($name) {
                return new $name($container);
            };
        }
    }
}