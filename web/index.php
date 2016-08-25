<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Aigisu\Core\Main;

$main = new Main();
$main->debug(true);
$main->bootstrap();
$main->run();
