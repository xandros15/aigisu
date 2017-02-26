<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Aigisu\Core\Configuration;

session_start();

$main = new \Slim\App(new Configuration());
require_once __DIR__ . '/../routes/routes.php';
$main->getContainer()->get(Illuminate\Database\Capsule\Manager::class); //for run capsule
$main->run();
