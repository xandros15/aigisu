<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-06-18
 * Time: 17:00
 */

use Api\Controllers\UserController as ApiUserController;
use Controllers\ImageFileController;
use Controllers\UnitController;
use Controllers\UserController;
use Middlewares\ApiException;
use Middlewares\FormAssets;
use Middlewares\HomeAssets;
use Middlewares\ShowQueries;
use Middlewares\TrailingSearch;
use Middlewares\TrailingSlash;

/** @var $this \Aigisu\Main */
$container = $this->getContainer();

$this->group('', function () use ($container) {
    $formAssetMiddleware = new FormAssets($container);
    $trailingSearchMiddleware = new TrailingSearch($container);
    $this->get('[/]', UnitController::class . ':actionIndex')
        ->setName('home')
        ->add($trailingSearchMiddleware);

    $this->group('/images', function () {
        /** @var $this \Aigisu\Main */
        $this->post('/upload/{id:\d+}', ImageFileController::class . ':actionCreate')
            ->setName('image.create');
    });

    $this->group('/units', function () use ($formAssetMiddleware) {
        /** @var $this \Aigisu\Main */
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

        $this->get('/delete/{id:\d+}}', UnitController::class . ':actionDelete')
            ->setName('unit.delete');

        $this->get('/images/{id:\d+}', UnitController::class . ':actionShowImages')
            ->setName('unit.images');
    });

    $this->group('/users', function () use ($formAssetMiddleware) {
        /** @var $this \Aigisu\Main */
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

})
    ->add(new TrailingSlash($container))
    ->add(new HomeAssets($container))
    ->add(new ShowQueries($container));


$this->group('/api', function () use ($container) {
    /** @var $this \Aigisu\Main */
    $this->group('/users', function () use ($container) {
        /** @var $this \Aigisu\Main */
        $this->post('/create', ApiUserController::class . ':actionCreate')
            ->setName('api.user.create');

        $this->get('', ApiUserController::class . ':actionIndex')
            ->setName('api.user.index');
        $this->get('/{id:\d+}', ApiUserController::class . ':actionView')
            ->setName('api.user.view');

        $this->patch('/update/{id:\d+}/password/', ApiUserController::class . ':actionChangePassword')
            ->setName('api.user.update.password');

        $this->delete('/delete/{id:\d+}', ApiUserController::class . ':actionDelete')
            ->setName('api.user.delete');
    });
})->add(new ApiException($container));