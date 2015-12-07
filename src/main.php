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
    defined('TB_NAME') || define('TB_NAME', 'units');
    defined('TB_IMAGES') || define('TB_IMAGES', 'images');
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
    echo renderPhpFile('layout');
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
    defined('SITE_URL') || define('SITE_URL', 'http://aigisu.pl/');
    defined('CONFIG_DIR') || define('CONFIG_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
    defined('VIEW_DIR') || define('VIEW_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR);
    defined('MAX_ROWS') || define('MAX_ROWS', 30);
    defined('DEBUG') || define('DEBUG', 0);
}

function urlQueryToGlobal()
{
    global $query;
    if (!empty($query->post->uploadImages) && !empty($query->files)) {
        uploadImages();
    }
    if (!empty($query->post->unit) && !empty($query->post->json)) {
        Units::editUnit();
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
    return SITE_URL . '?' . http_build_query($get);
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

function uploadImages()
{
    global $query;
    if (!empty($query->files)) {
        $upload = new UploadImages(Images::IMAGE_DIRECTORY);
        $upload->setExtentionServers();
        $upload->uploadFiles();
    }
}

// TODO create routing in other system
function isImageQuery()
{
    global $query;
    return (!empty($query->get->image));
}

function isLoginQuery()
{
    global $query;
    return (!empty($query->get->login));
}
