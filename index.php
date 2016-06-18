<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Aigisu\Main;

$main = new Main();
$main->debug(true);
$main->bootstrap();
$main->run();
