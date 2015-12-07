<?php

use RedBeanPHP\R;
use RedBeanPHP\OODBBean;
use app\google\GoogleFile;
use app\imgur\Imgur;
use models\Images;

defined('ROOT_DIR') || define('ROOT_DIR', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
require_once 'main.php';
configuration();
setAutoloader();
dbconnect();

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}
$images = R::find(Images::tableName(), ' `imgur` IS NULL ');

foreach ($images as $image) {
    R::begin();
    try {
        $imgur    = Imgur::facade();
        $imgur->setCatalog(trim($image->type, '12'));
        $imgur->setFilename(ROOT_DIR . Images::IMAGE_DIRECTORY . DIRECTORY_SEPARATOR . $image->id . '.png');
        $imgur->setDescription('R18');
        $imgur->setName($image->units->name);
        $response = $imgur->uploadFile();
        if (isset($response['data']['id']) && isset($response['data']['deletehash'])) {
            $image->imgur   = $response['data']['id'];
            $image->delhash = $response['data']['deletehash'];
            R::store($image);
            R::commit();
        }
    } catch (Exception $ex) {
        var_dump($ex);
        R::rollback();
    }
    var_dump($response);
    sleep(1);
}