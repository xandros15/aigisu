<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-07
 * Time: 23:36
 */
use Aigisu\Api\Controllers\UnitController as ApiUnitController;
use Aigisu\Api\Controllers\UserController as ApiUserController;
use Aigisu\Api\Middlewares\Validators\CreateUnitValidator;
use Aigisu\Api\Middlewares\Validators\CreateUserValidator;
use Aigisu\Api\Middlewares\Validators\UpdateUnitValidator;
use Aigisu\Api\Middlewares\Validators\UpdateUserValidator;

/** @var $this \Aigisu\Core\Main */
$container = $this->getContainer();
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