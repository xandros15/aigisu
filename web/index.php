<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Aigisu\Core\Configuration;
use Aigisu\Core\RouteProvider;

$main = new \Slim\App(new Configuration());
$routerProvider = new RouteProvider($main);
$routerProvider->map();
$main->getContainer()->get(Illuminate\Database\Capsule\Manager::class);
$main->run();
