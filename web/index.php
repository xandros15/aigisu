<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Aigisu\Core\Configuration;
use Aigisu\Core\RouteProvider;

$main = new \Slim\App(new Configuration());
!$this->getContainer()->get('isDebug') || $this->runSlimDebug();
$this->setRoutes();
$this->setDatabase();
$routerProvider = new RouteProvider($main);
$routerProvider->map();
$database = $this->getContainer()->get(Illuminate\Database\Capsule\Manager::class);
$database->setAsGlobal();
$database->bootEloquent();
$main->run();
