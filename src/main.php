<?php
$query = (object) [
        'get' => (object) $_GET,
        'post' => (object) $_POST,
        'files' => (object) $_FILES,
];

use RedBeanPHP\Facade as R;
use app\upload\UploadImages;
use models\Image;
use models\Unit;
use Slim\App as Slim;
use Slim\Container;
use controller\ImageController;
use controller\ImageFileController;
use controller\UnitController;
use controller\OauthController;
use models\Oauth;
use app\alert\Alert;

class Main
{
    static $app;

    function dbconnect()
    {
        require CONFIG_DIR . 'db.config.php';
        R::setup('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        R::debug(DEBUG);
        R::freeze();
    }

    function bootstrap()
    {
        static::$app = $this;
        $this->configuration();
        $this->setAutoloader();
        $this->dbconnect();
        $this->createSessions();
        $this->goSlimRoute();
    }

    function createSessions()
    {
        $alert = new Alert();
        $alert->init();
        $oauth = new Oauth();
        $oauth->run();
    }

    function setAutoloader()
    {
        require_once ROOT_DIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    }

    function renderPhpFile($_file_, $_params_ = [])
    {
        $filename = str_replace('/', DIRECTORY_SEPARATOR, $_file_);
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        require (VIEW_DIR . $filename . '.php');
        return ob_get_clean();
    }

    function configuration()
    {
        defined('SITE_URL') || define('SITE_URL', 'http://aigisu.pl/');
        defined('CONFIG_DIR') || define('CONFIG_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
        defined('VIEW_DIR') || define('VIEW_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR);
        defined('MAX_ROWS') || define('MAX_ROWS', 30);
        defined('DEBUG') || define('DEBUG', 0);
    }

    function generateLink(array $options)
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

    function reverseGet(stdClass $get, $name, $value)
    {
        return ($name == 'sort' && isset($get->{$name}) && strpos($get->{$name}, '-') !== 0) ? '-' . $value : $value;
    }

    function getSearchQuery()
    {
        global $query;
        if (empty($query->get->q)) {
            return '';
        }
        return $query->get->q;
    }

    function goSlimRoute()
    {
        $controllers               = [
            ImageController::class => function ($c) {
                return new ImageController($c);
            },
            ImageFileController::class => function ($c) {
                return new ImageFileController($c);
            },
            UnitController::class => function ($c) {
                return new UnitController($c);
            },
            OauthController::class => function ($c) {
                return new OauthController($c);
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

        $slim->run();
    }
}