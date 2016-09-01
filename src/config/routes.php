<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-06-18
 * Time: 17:00
 */

use Aigisu\Api\Controllers\UnitController as ApiUnitController;
use Aigisu\Api\Controllers\UserController as ApiUserController;
use Aigisu\Api\Middlewares\ExceptionHandler;
use Aigisu\Api\Middlewares\Validators\CreateUnitValidator;
use Aigisu\Api\Middlewares\Validators\CreateUserValidator;
use Aigisu\Api\Middlewares\Validators\UpdateUnitValidator;
use Aigisu\Api\Middlewares\Validators\UpdateUserValidator;
use Aigisu\Common\Controllers\ImageFileController;
use Aigisu\Common\Controllers\UnitController;
use Aigisu\Common\Controllers\UserController;
use Aigisu\Common\Middlewares\Alert;
use Aigisu\Common\Middlewares\FormAssets;
use Aigisu\Common\Middlewares\HomeAssets;
use Aigisu\Common\Middlewares\ShowQueries;
use Aigisu\Common\Middlewares\TrailingSearch;
use Aigisu\Common\Middlewares\TrailingSlash;
use Aigisu\Common\Middlewares\View;

/** @var $this \Aigisu\Core\Main */
$container = $this->getContainer();

$this->group('', function () use ($container) {
    $formAssetMiddleware = new FormAssets($container);
    $trailingSearchMiddleware = new TrailingSearch($container);
    $this->get('[/]', UnitController::class . ':actionIndex')
        ->setName('home')
        ->add($trailingSearchMiddleware);

    $this->group('/images', function () {
        /** @var $this \Aigisu\Core\Main */
        $this->post('/upload/{id:\d+}', ImageFileController::class . ':actionCreate')
            ->setName('image.create');
    });

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

        $this->get('/icon/{name:\w{32}}', UnitController::class . ':actionGetIcon')
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

})
    ->add(new TrailingSlash($container))
    ->add(new HomeAssets($container))
    ->add(new ShowQueries($container))
    ->add(new View($container))
    ->add(new Alert($container));


$this->group('/api', function () use ($container) {
    /** @var $this \Aigisu\Core\Main */
    $this->group('/users', function () use ($container) {
        /** @var $this \Aigisu\Core\Main */
        $this->post('', ApiUserController::class . ':actionCreate')
            ->setName('api.user.create')
            ->add(new CreateUserValidator($container));

        $this->get('', ApiUserController::class . ':actionIndex')
            ->setName('api.user.index');
        $this->get('/{id:\d+}', ApiUserController::class . ':actionView')
            ->setName('api.user.view');

        $this->patch('/{id:\d+}', ApiUserController::class . ':actionUpdate')
            ->setName('api.user.update')
            ->add(new UpdateUserValidator($container));

        $this->delete('/{id:\d+}', ApiUserController::class . ':actionDelete')
            ->setName('api.user.delete');
    });

    $this->group('/units', function () use ($container) {
        /** @var $this \Aigisu\Core\Main */
        $this->post('', ApiUnitController::class . ':actionCreate')
            ->setName('api.unit.create')
            ->add(new CreateUnitValidator($container));

        $this->get('', ApiUnitController::class . ':actionIndex')
            ->setName('api.unit.index');
        $this->get('/{id:\d+}', ApiUnitController::class . ':actionView')
            ->setName('api.unit.view');

        $this->post('/{id:\d+}', ApiUnitController::class . ':actionUpdate')
            ->setName('api.unit.update')
            ->add(new UpdateUnitValidator($container));

        $this->delete('/{id:\d+}', ApiUnitController::class . ':actionDelete')
            ->setName('api.unit.delete');

        $this->get('/rarities', ApiUnitController::class . ':actionRarities')
            ->setName('api.unit.rarities');
    });
})->add(new ExceptionHandler($container));