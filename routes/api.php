<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-07
 * Time: 23:36
 */
use Aigisu\Api\Controllers\Unit\CG\GoogleUploader;
use Aigisu\Api\Controllers\Unit\CGController;
use Aigisu\Api\Controllers\UnitController;
use Aigisu\Api\Controllers\UserController;
use Aigisu\Api\Middlewares\ParserUnitTagsMiddleware;
use Aigisu\Api\Middlewares\Validators\CreateCGValidator;
use Aigisu\Api\Middlewares\Validators\CreateUnitValidator;
use Aigisu\Api\Middlewares\Validators\CreateUserValidator;
use Aigisu\Api\Middlewares\Validators\MissingCGValidator;
use Aigisu\Api\Middlewares\Validators\UpdateCGValidator;
use Aigisu\Api\Middlewares\Validators\UpdateUnitValidator;
use Aigisu\Api\Middlewares\Validators\UpdateUserValidator;

/** @var $this \Aigisu\Core\Main */
$this->group('/users', function () {
    /** @var $this \Aigisu\Core\Main */
    $this->post('', UserController::class . ':actionCreate')
        ->setName('api.user.create')
        ->add(new CreateUserValidator($this->getContainer()));

    $this->get('', UserController::class . ':actionIndex')
        ->setName('api.user.index');
    $this->get('/{id:\d+}', UserController::class . ':actionView')
        ->setName('api.user.view');

    $this->patch('/{id:\d+}', UserController::class . ':actionUpdate')
        ->setName('api.user.update')
        ->add(new UpdateUserValidator($this->getContainer()));

    $this->delete('/{id:\d+}', UserController::class . ':actionDelete')
        ->setName('api.user.delete');
});

$this->group('/units', function () {
    /** @var $this \Aigisu\Core\Main */
    $this->post('', UnitController::class . ':actionCreate')
        ->setName('api.unit.create')
        ->add(new CreateUnitValidator($this->getContainer()))
        ->add(new ParserUnitTagsMiddleware($this->getContainer()));

    $this->get('', UnitController::class . ':actionIndex')
        ->setName('api.unit.index');
    $this->get('/{id:\d+}', UnitController::class . ':actionView')
        ->setName('api.unit.view');

    $this->post('/{id:\d+}', UnitController::class . ':actionUpdate')
        ->setName('api.unit.update')
        ->add(new UpdateUnitValidator($this->getContainer()))
        ->add(new ParserUnitTagsMiddleware($this->getContainer()));

    $this->delete('/{id:\d+}', UnitController::class . ':actionDelete')
        ->setName('api.unit.delete');

    $this->get('/rarities', UnitController::class . ':actionRarities')
        ->setName('api.unit.rarities');

    $this->group('/{unitId:\d+}/cg', function () {
        /** @var $this \Aigisu\Core\Main */
        $this->post('', CGController::class . ':actionCreate')
            ->setName('api.unit.cg.create')
            ->add(new MissingCGValidator($this->getContainer()))
            ->add(new CreateCGValidator($this->getContainer()));

        $this->get('', CGController::class . ':actionIndex')
            ->setName('api.unit.cg.index');
        $this->get('/{id:\d+}', CGController::class . ':actionView')
            ->setName('api.unit.cg.view');

        $this->post('/{id:\d+}', CGController::class . ':actionUpdate')
            ->setName('api.unit.cg.update')
            ->add(new MissingCGValidator($this->getContainer()))
            ->add(new UpdateCGValidator($this->getContainer()));

        $this->group('/{id:\d+}/google', function () {
            $this->post('', GoogleUploader::class . ':actionCreate')
                ->setName('api.unit.cg.google.create');
            $this->patch('', GoogleUploader::class . ':actionUpdate')
                ->setName('api.unit.cg.google.update');
            $this->delete('', GoogleUploader::class . ':actionDelete')
                ->setName('api.unit.cg.google.delete');
        });

        $this->delete('/{id:\d+}', CGController::class . ':actionDelete')
            ->setName('api.unit.cg.delete');
    });
});