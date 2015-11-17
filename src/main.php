<?php
$query    = (object) ['get' => (object) $_GET];
$colNames = ['id', 'name', 'orginal', 'rarity'];

function dbconnect()
{
    require __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.config.php';
    require __DIR__ . DIRECTORY_SEPARATOR . 'rb.php';
    R::setup('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    R::debug(DEBUG);
}

function editUnit($unitPost)
{
    if (preg_match('/^[\d]+$/', $unitPost['id'])) {
        $unit = R::load(TB_NAME, $unitPost['id']);
        if (preg_match('/^[\w]+$/', $unitPost['name'])) {
            $unit->name                 = $unitPost['name'];
            $_POST['valid'][0]['value'] = true;
            $_POST['valid'][0]['name']  = 'unit-name';
        } else {
            $_POST['valid'][0]['value'] = false;
            $_POST['valid'][0]['name']  = 'unit-name';
        }
//        if (preg_match('/^[\d]+$/', $unitPost['class'])) {
//            $class                      = R::load('class', $unitPost['class']);
//            $unit->class                = $class;
//            $_POST['valid'][1]['value'] = true;
//            $_POST['valid'][1]['name']  = 'unit-class';
//        } else {
//            $_POST['valid'][1]['value'] = false;
//            $_POST['valid'][1]['name']  = 'unit-class';
//        }
        if (!empty($unitPost['rarity'])) {
            $unit->rarity               = $unitPost['rarity'];
            $_POST['valid'][2]['value'] = true;
            $_POST['valid'][2]['name']  = 'unit-rarity';
        } else {
            $_POST['valid'][2]['value'] = false;
            $_POST['valid'][2]['name']  = 'unit-rarity';
        }
        R::store($unit);
        die(json_encode($_POST));
    }
    $_POST['valid'] = false;
    die(json_encode($_POST));
}

/**
 *
 * @return Array
 */
function enumRarity()
{
    $enumRow = R::getRow("SHOW COLUMNS FROM units LIKE 'rarity'");
    $enum    = $enumRow['Type'];
    $enum    = rtrim($enum, ')');
    $enum    = ltrim($enum, 'enum(');
    $enum    = str_replace('\'', '', $enum);
    return $enum    = explode(',', $enum);
}

function createClass($class)
{
    foreach ($class as $id => $valid) {
        if (!preg_match('/^\w+(?:\s{1}\w+)?(?:\s{1}\w+)?$/', $valid)) {
            return;
        }
        $class[$id] = ucwords($valid);
    }
    $className   = implode('/', $class);
    $class       = R::dispense('class');
    $class->name = $className;
    R::store($class);
}

function bootstrap()
{
    configuration();
    dbconnect();
    urlQueryToGlobal();
    echo renderPhpFile('layout');
}

function renderPhpFile($_file_, $_params_ = [])
{
    ob_start();
    ob_implicit_flush(false);
    extract($_params_, EXTR_OVERWRITE);
    require (VIEW_DIR . DIRECTORY_SEPARATOR . $_file_ . '.php');
    return ob_get_clean();
}

function configuration()
{
    defined('VIEW_DIR') || define('VIEW_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'view');
    defined('MAX_ROWS') || define('MAX_ROWS', 30);
    defined('DEBUG') || define('DEBUG', 1);
}

function findUnits()
{
    $start = MAX_ROWS * max(getCurrentPage() - 1, 0);
    $sort  = getCurrentSort();

    $bindings = [
        ':start' => min($start, getMaxResults() - MAX_ROWS),
        ':limit' => MAX_ROWS
    ];
    $search   = search($sort, $bindings);
    return ($search !== false) ? $search : getAllUnits($sort, $bindings);
}

function getAllUnits($sort, $bindings)
{
    return R::findAll(TB_NAME, $sort . ' LIMIT :start,:limit', $bindings);
}

function getMaxResults($query = '', array $bindings = [])
{
    return R::count(TB_NAME, $query, $bindings);
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
    if (!empty($_POST['unit'])) {
        editUnit($_POST['unit']);
    } elseif (!empty($_POST['class'])) {
        createClass($_POST['class']);
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
    return "http://{$_SERVER['HTTP_HOST']}/?" . http_build_query($get);
}

function reverseGet(stdClass $get, $name, $value)
{
    return ($name == 'sort' && isset($get->{$name}) && strpos($get->{$name}, '-') !== 0) ? '-' . $value : $value;
}

function getMaxPages()
{
    $search     = search();
    $maxResults = ($search !== false) ? count(search()) : R::count(TB_NAME);
    return ceil($maxResults / MAX_ROWS);
}

function search($order = '', array $bindings = [])
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
        if (count($bindings) == 2) {
            $order = $order . ' LIMIT :start,:limit ';
            foreach($bindings as $bind => $value){
                $order = str_replace($bind, $value, $order);
            }
        }
        $bean = R::findLike(TB_NAME, $search, $order);
    } catch (RedBeanPHP\RedException $exc) {
        var_dump($exc->getMessage(), $exc->getTrace());
        return false;
    }
    return $bean;
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
