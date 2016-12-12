<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-14
 * Time: 18:09
 */
use Aigisu\Storage\Controllers\ImageController;
use Aigisu\Storage\Controllers\SpriteController;

/** @var $this \Aigisu\Core\Main */

$this->get('/images/{path:[\w/]+}', ImageController::class . ':actionView')->setName('storage.images');
$this->get('/icons/sprite.css', SpriteController::class . ':getIconsStylesheet')->setName('storage.icons.stylesheet');
$this->get('/icons/sprite', SpriteController::class . ':getIconsSprite')->setName('storage.icons.sprite');
