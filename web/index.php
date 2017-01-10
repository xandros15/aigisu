<?php
defined('ROOT_DIR') || define('ROOT_DIR', realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR);
defined('WEB_DIR') || define('WEB_DIR', ROOT_DIR . '/web/');

require_once ROOT_DIR . 'src/main.php';

$main = new Main();

$main->bootstrap();
$main->run();
