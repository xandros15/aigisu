<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-06-18
 * Time: 17:00
 */

use Controllers\ImageController;
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

$this->map(['get'], '/[unit]', UnitController::class . ':actionIndex')->setName('home')->add($trailingSearchMiddleware);
$this->group('/image', function () {
    /** @var $this \Aigisu\Main */
    $this->map(['post'], '/upload/{id:\d+}', ImageFileController::class . ':actionCreate')->setName('imageUpload');
    $this->map(['get'], '/{id:\d+}', ImageController::class . ':actionIndex')->setName('image');
});
$this->group('/unit', function () use ($formAssetMiddleware) {
    /** @var $this \Aigisu\Main */
    $this->map(['get'], '/edit/{id:\d+}', UnitController::class . ':actionView')
        ->setName('unitView')
        ->add($formAssetMiddleware);
    $this->map(['get'], '/create', UnitController::class . ':actionView')
        ->setName('unitCreate')
        ->add($formAssetMiddleware);
    $this->map(['post'], '/update/{id:\d+}/confirm', UnitController::class . ':actionUpdate')->setName('unitUpdate');
    $this->map(['post'], '/create', UnitController::class . ':actionCreate')->setName('unitCreate');
    $this->map(['get'], '/delete/{id:\d+}/confirm', UnitController::class . ':actionDelete')->setName('unitDelete');
});

$this->group('/user', function () use ($formAssetMiddleware) {
    /** @var $this \Aigisu\Main */
    $this->get('[/]', UserController::class . ':actionIndex')->setName('user.index');
    $this->post('/create', UserController::class . ':actionCreate')
        ->setName('user.create')
        ->add($formAssetMiddleware);
    $this->get('/view/{id:\d+}', UserController::class . ':actionView')->setName('user.view');
    $this->get('/edit/{id:\d+}', UserController::class . ':actionEdit')
        ->setName('user.edit')
        ->add($formAssetMiddleware);
    $this->post('/update/{id:\d+}', UserController::class . ':actionUpdate')->setName('user.update');
    $this->get('/delete/{id:\d+}', UserController::class . ':actionDelete')->setName('user.delete');
});
$this->add(new TrailingSlash($this->getContainer()));
$this->add(new HomeAssets($this->getContainer()));

if ($this->isDebug()) {
    $this->add(new ShowQueries($this->getContainer()));
}