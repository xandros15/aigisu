<?php
$query = (object) [
        'get' => (object) $_GET,
        'post' => (object) $_POST,
        'files' => (object) $_FILES,
];

use RedBeanPHP\Facade as R;
use app\upload\UploadImages;
use models\Images;
use models\Units;

function dbconnect()
{
    require CONFIG_DIR . 'db.config.php';
    R::setup('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    R::debug(DEBUG);
    R::freeze();
}

function bootstrap()
{
    configuration();
    setAutoloader();
    dbconnect();
    createSessions();
    urlQueryToGlobal();
    goSlimRoute();
}

use models\Oauth;
use app\alert\Alert;

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
    defined('SITE_URL') || define('SITE_URL', 'http://aigis.pl/');
    defined('CONFIG_DIR') || define('CONFIG_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
    defined('VIEW_DIR') || define('VIEW_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR);
    defined('MAX_ROWS') || define('MAX_ROWS', 30);
    defined('DEBUG') || define('DEBUG', 0);
}

function urlQueryToGlobal()
{
    global $query;
    if (!empty($query->post->uploadImages)) {
        $upload = new UploadImages(Images::IMAGE_DIRECTORY);
        $upload->upload();
    }
    if (!empty($query->post->updateUnit)) {
//        Units::editUnit($query->post);
    }
}

function generateLink(array $options)
{
    global $query;
    if (!isset($query->get)) {
        $get = $options;
    } else {
        $get = clone $query->get;
        foreach ($options as $name => $value) {
            $get->{$name} = reverseGet($get, $name, $value);
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

use Slim\App as Slim;
use Slim\Container;
use controller\ImagesController;
use controller\UnitsController;
use controller\OauthController;

function goSlimRoute()
{
    $controllers           = [
        ImagesController::class => function ($c) {
            return new ImagesController($c);
        },
        UnitsController::class => function ($c) {
            return new UnitsController($c);
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

    $slim->get('/image/{id}', ImagesController::class . ':actionIndex')->setName('images');
    $slim->get('/', UnitsController::class . ':actionIndex')->setName('home');
    $slim->group('/units',
        function() {
        $this->get('[/]', UnitsController::class . ':actionIndex')->setName('units');
        $this->post('/update/{id:\d+}', UnitsController::class . ':actionUpdate')->setName('unitsUpdate');
    });
    $slim->group('/oauth',
        function () {
        $this->get('[/]', OauthController::class . ':actionIndex')->setName('oauth');
        $this->post('/login', OauthController::class . ':actionLogin')->setName('login');
        $this->post('/logout', OauthController::class . ':actionLogout')->setName('logout');
    });

    $slim->run();
}
