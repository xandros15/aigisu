<?php
namespace Aigisu;

use Aigisu\Alert\Alert;
use Aigisu\View\View;
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
        $database->setValidator($settings->locale, Configuration::DIR_CONFIG . 'lang');
        $database->setAsGlobal();
        $database->bootEloquent();
        $this->getContainer()['connection'] = $database->connection();
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
}