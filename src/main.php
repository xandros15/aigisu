<?php
$query = (object) [
        'get' => (object) $_GET,
        'post' => (object) $_POST,
        'files' => (object) $_FILES,
];

use RedBeanPHP\Facade as R;
use Slim\App as Slim;
use Slim\Router;
use models\Oauth;
use app\alert\Alert;
use app\core\Configuration;
use app\slim\SlimConfig;

class Main
{
    /** @var Main */
    static $app;

    /** @var Slim */
    public $slim;

    /** @var Router */
    public $router;

    /** @var Configuration */
    public $web;

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

    public function generateLink(array $options)
    {
        global $query;
        if (!isset($query->get)) {
            $get = $options;
        } else {
            $get = clone $query->get;
            foreach ($options as $name => $value) {
                $get->{$name} = $this->reverseGet($get, $name, $value);
            }
        }
        return '?' . http_build_query($get);
    }

    public function reverseGet(stdClass $get, $name, $value)
    {
        return ($name == 'sort' && isset($get->{$name}) && strpos($get->{$name}, '-') !== 0) ? '-' . $value : $value;
    }

    public function getSearchQuery()
    {
        global $query;
        if (empty($query->get->q)) {
            return '';
        }
        return $query->get->q;
    }

    private function configuration()
    {
        $this->web = new Configuration;
    }

    private function dbconnect()
    {
        require CONFIG_DIR . 'db.config.php';
        R::setup('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        R::debug(DEBUG);
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