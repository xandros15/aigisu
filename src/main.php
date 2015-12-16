<?php
$query = (object) [
        'get' => (object) $_GET,
        'post' => (object) $_POST,
        'files' => (object) $_FILES,
];

use RedBeanPHP\Facade as R;
use Slim\App as Slim;
use Slim\Container;
use Slim\Router;
use controller\ImageController;
use controller\ImageFileController;
use controller\UnitController;
use controller\OauthController;
use models\Oauth;
use app\alert\Alert;
use app\core\Configuration;

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
        $controllers               = [
            ImageController::class => function () {
                return new ImageController();
            },
            ImageFileController::class => function () {
                return new ImageFileController();
            },
            UnitController::class => function () {
                return new UnitController();
            },
            OauthController::class => function () {
                return new OauthController();
            },
            'settings' => [
                'displayErrorDetails' => true,
            ],
        ];

        $container = new Container($controllers);
        $slim      = new Slim($container);

        $slim->get('/', UnitController::class . ':actionIndex')->setName('home');
        $slim->group('/image',
            function() {
            $this->post('/upload/{id:\d+}', ImageFileController::class . ':actionCreate')->setName('imageUpload');
            $this->get('/{id}', ImageController::class . ':actionIndex')->setName('image');
        });
        $slim->group('/unit',
            function() {
            $this->get('[/]', UnitController::class . ':actionIndex')->setName('unit');
            $this->post('/update/{id:\d+}', UnitController::class . ':actionUpdate')->setName('unitUpdate');
        });
        $slim->group('/oauth',
            function () {
            $this->get('[/]', OauthController::class . ':actionIndex')->setName('oauth');
            $this->post('/login', OauthController::class . ':actionLogin')->setName('login');
            $this->post('/logout', OauthController::class . ':actionLogout')->setName('logout');
        });

        $this->slim   = $slim;
        $this->router = $container->router;
    }
}