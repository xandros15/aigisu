<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-07
 * Time: 23:36
 */
use Aigisu\Api\Controllers\UnitController;
use Aigisu\Api\Controllers\UserController;
use Aigisu\Api\Middlewares\UnitEventMiddleware;
use Aigisu\Api\Middlewares\Validators\CreateUnitValidator;
use Aigisu\Api\Middlewares\Validators\CreateUserValidator;
use Aigisu\Api\Middlewares\Validators\UpdateUnitValidator;
use Aigisu\Api\Middlewares\Validators\UpdateUserValidator;

/** @var $this \Aigisu\Core\Main */
/** @var $middlewares \Aigisu\Core\MiddlewareProvider */
$middlewares = $this->getContainer()->get('middlewares');
$this->group('/users', function () use ($middlewares) {
    /** @var $this \Aigisu\Core\Main */
    $this->post('', UserController::class . ':actionCreate')
        ->setName('api.user.create')
        ->add($middlewares->createMiddleware(CreateUserValidator::class));

    $this->get('', UserController::class . ':actionIndex')
        ->setName('api.user.index');
    $this->get('/{id:\d+}', UserController::class . ':actionView')
        ->setName('api.user.view');

    $this->patch('/{id:\d+}', UserController::class . ':actionUpdate')
        ->setName('api.user.update')
        ->add($middlewares->createMiddleware(UpdateUserValidator::class));

    $this->delete('/{id:\d+}', UserController::class . ':actionDelete')
        ->setName('api.user.delete');
});

$this->group('/units', function () use ($middlewares) {
    /** @var $this \Aigisu\Core\Main */
    $this->post('', UnitController::class . ':actionCreate')
        ->setName('api.unit.create')
        ->add($middlewares->createMiddleware(CreateUnitValidator::class));

    $this->get('', UnitController::class . ':actionIndex')
        ->setName('api.unit.index');
    $this->get('/{id:\d+}', UnitController::class . ':actionView')
        ->setName('api.unit.view');

    $this->post('/{id:\d+}', UnitController::class . ':actionUpdate')
        ->setName('api.unit.update')
        ->add($middlewares->createMiddleware(UpdateUnitValidator::class));

    $this->delete('/{id:\d+}', UnitController::class . ':actionDelete')
        ->setName('api.unit.delete');

    $this->get('/rarities', UnitController::class . ':actionRarities')
        ->setName('api.unit.rarities');
})->add($middlewares->createMiddleware(UnitEventMiddleware::class));