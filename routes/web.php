<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-07
 * Time: 23:36
 */

use Aigisu\Common\Controllers\HomeController;
use Aigisu\Common\Controllers\ImageFileController;
use Aigisu\Common\Controllers\UnitController;
use Aigisu\Common\Controllers\UserController;
use Aigisu\Common\Middlewares\FormAssets;

/** @var $this \Aigisu\Core\Main */

$formAssetMiddleware = new FormAssets($this->getContainer());

$this->get('[/]', HomeController::class . ':actionIndex')
    ->setName('home');

$this->group('/images', function () {
    /** @var $this \Aigisu\Core\Main */
    $this->post('/upload/{id:\d+}', ImageFileController::class . ':actionCreate')
        ->setName('image.create');
});

$this->get('/upload/image/{id:\d+}', UnitController::class . ':actionGetHelpImage')
    ->setName('help.image');

$this->group('/units', function () use ($formAssetMiddleware) {
    /** @var $this \Aigisu\Core\Main */
    $this->map(['post', 'get'], '/create', UnitController::class . ':actionCreate')
        ->setName('unit.create')
        ->add($formAssetMiddleware);

    $this->get('', UnitController::class . ':actionIndex')
        ->setName('unit.index');
    $this->get('/{id:\d+}', UnitController::class . ':actionView')
        ->setName('unit.view');

    $this->map(['get', 'post'], '/update/{id:\d+}', UnitController::class . ':actionUpdate')
        ->setName('unit.update')
        ->add($formAssetMiddleware);

    $this->get('/delete/{id:\d+}', UnitController::class . ':actionDelete')
        ->setName('unit.delete');

    $this->get('/images/{id:\d+}', UnitController::class . ':actionShowImages')
        ->setName('unit.images');

    $this->get('/icon', UnitController::class . ':actionGetIcon')
        ->setName('unit.icon');
});

$this->group('/users', function () use ($formAssetMiddleware) {
    /** @var $this \Aigisu\Core\Main */
    $this->map(['post', 'get'], '/create', UserController::class . ':actionCreate')
        ->setName('user.create')
        ->add($formAssetMiddleware);

    $this->get('', UserController::class . ':actionIndex')
        ->setName('user.index');
    $this->get('/{id:\d+}', UserController::class . ':actionView')
        ->setName('user.view');

    $this->map(['post', 'get'], '/update/{id:\d+}', UserController::class . ':actionUpdate')
        ->setName('user.update')
        ->add($formAssetMiddleware);

    $this->get('/delete/{id:\d+}', UserController::class . ':actionDelete')
        ->setName('user.delete');
});
