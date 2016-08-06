<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Aigisu\Main;

$main = new Main();
$main->debug(true);
$main->bootstrap();
$main->run();
