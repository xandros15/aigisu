<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-14
 * Time: 18:09
 */
use Aigisu\Storage\Controllers\ImageController;

/** @var $this \Slim\App */
$this->get('/images/{path:[\w/]+}', ImageController::class . ':actionView')->setName('storage.images');
