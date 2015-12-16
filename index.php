<?php
defined('ROOT_DIR') || define('ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR);

require_once ROOT_DIR . 'src' . DIRECTORY_SEPARATOR . 'main.php';

$main = new Main;

$main->bootstrap();
$main->run();
