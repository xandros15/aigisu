<?php
namespace Aigisu;

use Aigisu\Alert\Alert;
use Aigisu\View\SlimViewExtension;
use Aigisu\View\View;
use Slim\App as Slim;
use Slim\Container;

class Main extends Slim
{
    public function __construct($items = [])
    {
        parent::__construct(new Configuration($items));
    }

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
        $this->getContainer()['view'] = function (Container $container) {
            $view = new View(Configuration::DIR_VIEW);
            $slimViewExtension = new SlimViewExtension($container);
            $view->addExtension($slimViewExtension);
            return $view;
        };
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