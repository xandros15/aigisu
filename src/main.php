<?php
$query    = (object) [
        'get' => (object) $_GET,
        'post' => (object) $_POST,
        'files' => (object) $_FILES,
];
$colNames = ['id', 'name', 'orginal', 'rarity'];

function dbconnect()
{
    require __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.config.php';
    require __DIR__ . DIRECTORY_SEPARATOR . 'rb.php';
    defined('TB_NAME') || define('TB_NAME', 'units');
    defined('TB_IMAGES') || define('TB_IMAGES', 'images');
    R::setup('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    R::debug(DEBUG);
}

function editUnit($unitPost)
{
    R::debug(false);
    if (isset($unitPost['id']) && preg_match('/^[\d]+$/', $unitPost['id'])) {
        $unit = R::load(TB_NAME, $unitPost['id']);
        if (preg_match('/^[\w]+$/', $unitPost['name'])) {
            $unit->name                 = $unitPost['name'];
            $_POST['valid'][0]['value'] = true;
            $_POST['valid'][0]['name']  = 'unit-name';
        } else {
            $_POST['valid'][0]['value'] = false;
            $_POST['valid'][0]['name']  = 'unit-name';
        }
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
    defined('DEBUG') || define('DEBUG', 0);
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
    if (!empty($_POST['unit'])) {
        if (!empty($query->files)) {
            uploadImages();
        } else {
            editUnit($_POST['unit']);
        }
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
    require_once 'upload.php';
    if (!empty($query->files)) {
        $upload = Upload::factory('tmp');
        foreach ($query->files as $input => $file) {
            if ($file['error']) {
                continue;
            }
            $upload->file($file);
            $validation = new FileValidator();
            $upload->callbacks($validation, ['haveCorrectSize', 'isInDatabase']);
            $upload->set_max_file_size(1);
            $upload->set_allowed_mime_types(['image/png']);
            $results    = $upload->upload();
            $errors     = $upload->get_errors();
            if ($errors) {
                var_dump($errors);
            } else {
                addImageToDatabase($results, $input);
            }
        }
    }
}

function isDisabledUpload(RedBeanPHP\OODBBean $object, $name)
{
    return ($object->{$name . '_id'}) || in_array($object->rarity, ['iron', 'bronze']);
}

function isImageQuery()
{
    global $query;
    return (!empty($query->get->image));
}

function getImageFile(RedBeanPHP\OODBBean $image)
{
    return "http://{$_SERVER['HTTP_HOST']}/images/{$image->id}.png";
}

function getImagesFromDb()
{
    global $query;
    $id   = $query->get->image;
    $unit = R::load(TB_NAME, (int) $id);
    R::aliases([
        'nutaku1' => 'images',
        'nutaku2' => 'images',
        'dmm1' => 'images',
        'dmm2' => 'images'
    ]);
    if (!$unit) {
        return [];
    }
    return ['dmm1' => $unit->dmm1, 'dmm2' => $unit->dmm2, 'nutaku1' => $unit->nutaku1, 'nutaku2' => $unit->nutaku2];
}

function addImageToDatabase(array $results, $input)
{
    global $query;
    $newDir = ROOT_DIR . DIRECTORY_SEPARATOR . 'images';
    if (!is_dir($newDir)) {
        mkdir($newDir, 755);
    }
    R::begin();
    try {
        $image      = R::dispense(TB_IMAGES);
        $image->md5 = md5_file($results['full_path']);
        $unit       = R::load(TB_NAME, (int) $query->post->id);
        if (!is_string($input)) {
            throw new Exception('input isn\'t string');
        }
        if (!$unit) {
            throw new Exception('wrong id');
        }
        $unit->{$input} = $image;
        R::storeAll([$unit, $image]);
        if (!rename($results['full_path'],
                $newDir . DIRECTORY_SEPARATOR . $image->getID() . '.' . pathinfo($results['original_filename'],
                    PATHINFO_EXTENSION))) {
            throw new Exception('Can\t rename file');
        }
        if (is_file($results['full_path']) && is_executable($results['full_path'])) {
            unlink($results['full_path']);
        }
        R::commit();
    } catch (Exception $exc) {
        var_dump($exc->getMessage());
        R::rollback();
    }
}

class FileValidator
{
    const MAX_WIDTH  = 960;
    const MAX_HEIGHT = 640;

    public function haveCorrectSize(Upload $object)
    {
        $imageSize = getimagesize($object->file['tmp_name']);
        if ($imageSize[0] > self::MAX_WIDTH) {
            $object->set_error('Image ' . $object->file['original_filename'] . ' has too much width');
        }
        if ($imageSize[1] > self::MAX_HEIGHT) {
            $object->set_error('Image ' . $object->file['original_filename'] . ' has too much heigh');
        }
    }

    public function isInDatabase(Upload $object)
    {
        $md5Temp = md5_file($object->file['tmp_name']);
        if (R::find(TB_IMAGES, 'md5 = :md5', [':md5' => $md5Temp])) {
            $object->set_error('Image ' . $object->file['original_filename'] . ' exists in Database');
        }
    }
}