<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-06-18
 * Time: 17:00
 */

use Controllers\ImageFileController;
use Controllers\UnitController;
use Controllers\UserController;
use Middlewares\FormAssets;
use Middlewares\HomeAssets;
use Middlewares\ShowQueries;
use Middlewares\TrailingSearch;
use Middlewares\TrailingSlash;

/** @var $this \Aigisu\Main */

$formAssetMiddleware = new FormAssets($this->getContainer());
$trailingSearchMiddleware = new TrailingSearch($this->getContainer());

$this->get('[/]', UnitController::class . ':actionIndex')
    ->setName('home')
    ->add($trailingSearchMiddleware);

$this->group('/image', function () {
    /** @var $this \Aigisu\Main */
    $this->post('/upload/{id:\d+}', ImageFileController::class . ':actionCreate')
        ->setName('image.create');
});

$this->group('/unit', function () use ($formAssetMiddleware) {
    /** @var $this \Aigisu\Main */
    $this->post('/create', UnitController::class . ':actionCreate')
        ->setName('unit.create');
    $this->get('/create', UnitController::class . ':actionCreate')
        ->setName('unit.create')
        ->add($formAssetMiddleware);

    $this->get('s', UnitController::class . ':actionIndex')
        ->setName('unit.index');
    $this->get('/{id:\d+}', UnitController::class . ':actionView')
        ->setName('unit.view');

    $this->map(['get', 'post'], '/update/{id:\d+}', UnitController::class . ':actionUpdate')
        ->setName('unit.update')
        ->add($formAssetMiddleware);

    $this->get('/delete/{id:\d+}}', UnitController::class . ':actionDelete')
        ->setName('unit.delete');

    $this->get('/images/{id:\d+}', UnitController::class . ':actionShowImages')
        ->setName('unit.images');
});

$this->group('/user', function () use ($formAssetMiddleware) {
    /** @var $this \Aigisu\Main */
    $this->post('/create', UserController::class . ':actionCreate')
        ->setName('user.create');
    $this->get('/create', UserController::class . ':actionCreate')
        ->setName('user.create')
        ->add($formAssetMiddleware);

    $this->get('s', UserController::class . ':actionIndex')
        ->setName('user.index');
    $this->get('/{id:\d+}', UserController::class . ':actionView')
        ->setName('user.view');

    $this->get('/update/{id:\d+}', UserController::class . ':actionUpdate')
        ->setName('user.update')
        ->add($formAssetMiddleware);
    $this->post('/update/{id:\d+}', UserController::class . ':actionUpdate')
        ->setName('user.update');

    $this->get('/delete/{id:\d+}', UserController::class . ':actionDelete')
        ->setName('user.delete');
});

$this->add(new TrailingSlash($this->getContainer()));
$this->add(new HomeAssets($this->getContainer()));

if ($this->isDebug()) {
    $this->add(new ShowQueries($this->getContainer()));
}