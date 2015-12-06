<?php
$query    = (object) [
        'get' => (object) $_GET,
        'post' => (object) $_POST,
        'files' => (object) $_FILES,
];
$colNames = ['id', 'name', 'orginal', 'rarity'];

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

function editUnit()
{
    R::debug(false);
    global $query;
    $unitPost = (object) $query->post->unit;
    if (isset($unitPost->id) && preg_match('/^\d+$/', $unitPost->id)) {
        $response = ['valid' => []];
        $unit     = R::load(TB_NAME, $unitPost->id);
        if (preg_match('/^[\w]+$/', $unitPost->name)) {
            $unit->name                    = $unitPost->name;
            $response['valid'][0]['value'] = true;
            $response['valid'][0]['name']  = 'unit-name';
        } else {
            $response['valid'][0]['value'] = false;
            $response['valid'][0]['name']  = 'unit-name';
        }
        if (!empty($unitPost->rarity) && in_array($unitPost->rarity, Units::getRarities())) {
            $unit->rarity                  = $unitPost->rarity;
            $response['valid'][2]['value'] = true;
            $response['valid'][2]['name']  = 'unit-rarity';
        } else {
            $response['valid'][2]['value'] = false;
            $response['valid'][2]['name']  = 'unit-rarity';
        }
        R::store($unit);
        die(json_encode($response));
    }
    $_POST['valid'] = false;
    die(json_encode($_POST));
}

/**
 *
 * @return Array
 */
function getEnumRarity()
{
    $enumRow = R::getRow("SHOW COLUMNS FROM units LIKE 'rarity'");
    $enum    = $enumRow['Type'];
    $enum    = rtrim($enum, ')');
    $enum    = ltrim($enum, 'enum(');
    $enum    = str_replace('\'', '', $enum);
    return $enum    = explode(',', $enum);
}

function bootstrap()
{
    configuration();
    setAutoloader();
    dbconnect();
    urlQueryToGlobal();
    echo renderPhpFile('layout');
}

function setAutoloader()
{
    require_once ROOT_DIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
}

function renderPhpFile($_file_, $_params_ = [])
{
    ob_start();
    ob_implicit_flush(false);
    extract($_params_, EXTR_OVERWRITE);
    require (VIEW_DIR . $_file_ . '.php');
    return ob_get_clean();
}

function configuration()
{
    defined('SITE_URL') || define('SITE_URL', 'http://aigisu.pl/');
    defined('CONFIG_DIR') || define('CONFIG_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
    defined('VIEW_DIR') || define('VIEW_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR);
    defined('MAX_ROWS') || define('MAX_ROWS', 30);
    defined('DEBUG') || define('DEBUG', 1);
}

function findUnits()
{

    $sort = getCurrentSort();


    $search = search($sort);
    return ($search !== false) ? $search : getAllUnits($sort);
}

function getAllUnits($sort)
{
    $start    = MAX_ROWS * max(getCurrentPage() - 1, 0);
    $bindings = [
        ':start' => $start,
        ':limit' => MAX_ROWS
    ];
    return R::findAll(TB_NAME, $sort . ' LIMIT :start,:limit', $bindings);
}

function getCurrentSort()
{
    global $query, $colNames;

    $direction = 'DESC';
    $col       = 'id';
    if (isset($query->get->sort)) {
        $order = strtolower($query->get->sort);
        if (strpos($order, '-') === 0) {
            $order     = substr($order, 1);
            $direction = 'ASC';
        }
        foreach ($colNames as $colName) {
            if ($colName == $order) {
                $col = strtolower($order);
                break;
            }
        }
    }

    return 'ORDER BY ' . $col . ' ' . $direction;
}

function getCurrentPage()
{
    global $query;
    return (isset($query->get->page) && preg_match('/^\d+$/', $query->get->page)) ? $query->get->page : 1;
}

function urlQueryToGlobal()
{
    global $query;
    if (!empty($query->post->uploadImages) && !empty($query->files)) {
        uploadImages();
    }
    if (!empty($query->post->unit) && !empty($query->post->json)) {
        editUnit();
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

function getMaxPages()
{
    $search     = search();
    $maxResults = ($search !== false) ? count(search()) : R::count(TB_NAME);
    return (int) ceil($maxResults / MAX_ROWS);
}

function search($order = '')
{
    global $query;
    global $colNames;
    if (empty($query->get->q)) {
        return false;
    }
    $search = parseSearch($query->get->q);
    try {
        foreach ($search as $colName => $result) {
            if (!in_array($colName, $colNames) || !is_array($result)) {
                unset($search[$colName]);
            }
        }
        if ($order) {
            $order = bindLimit($order);
        }
        $bean = R::findLike(TB_NAME, $search, $order);
    } catch (RedBeanPHP\RedException $exc) {
        var_dump($exc->getMessage(), $exc->getTrace());
        return false;
    }
    return $bean;
}

function bindLimit($sql)
{
    $start    = MAX_ROWS * max(getCurrentPage() - 1, 0);
    $bindings = [
        ':start' => $start,
        ':limit' => MAX_ROWS
    ];
    if (count($bindings) == 2) {
        $sql = $sql . ' LIMIT :start,:limit ';
        foreach ($bindings as $bind => $value) {
            $sql = str_replace($bind, abs((int) $value), $sql);
        }
    }
    return $sql;
}

/**
 * 
 * @param string $search
 * @return array
 */
function parseSearch($search)
{
    $arguments    = explode(' ', $search);
    $newArguments = [];
    foreach ($arguments as $argument) {
        $namespace = 'name';
        if (preg_match('/^(.+):(.+)$/', $argument, $matches)) {
            $namespace = $matches[1];
            $argument  = $matches[2];
        }
        if (strpos($argument, '*') === 0) {
            $argument = substr_replace($argument, '%', 0, 1);
        }
        if (strrpos($argument, '*') === strlen($argument) - 1) {
            $argument = substr_replace($argument, '%', -1, 1);
        }
        $newArguments[$namespace][] = $argument;
    }
    return $newArguments;
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
        $upload->uploadFiles();
    }
}

// TODO create routing in other system
function isImageQuery()
{
    global $query;
    return (!empty($query->get->image));
}
