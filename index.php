<?php
defined('ROOT_DIR') || define('ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR);
require_once ROOT_DIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
use Aigisu\Main;

$main = new Main();
$main->run();
