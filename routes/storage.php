<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-14
 * Time: 18:09
 */
use Aigisu\Storage\Controllers\ImageController;

/** @var $this \Aigisu\Core\Main */
/** @var $middlewares \Aigisu\Core\MiddlewareProvider */

$this->get('/images/{path:\w+/\w{32}}', ImageController::class . ':actionView')
    ->setName('storage.images');