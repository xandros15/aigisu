<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-07
 * Time: 23:36
 */
use Aigisu\Api\Controllers\AuthController;
use Aigisu\Api\Controllers\Unit\CG\GoogleUploader;
use Aigisu\Api\Controllers\Unit\CG\ImgurUploader;
use Aigisu\Api\Controllers\Unit\CGController;
use Aigisu\Api\Controllers\UnitController;
use Aigisu\Api\Controllers\UserController;
use Aigisu\Api\Middlewares\Access\AdminAccessMiddleware;
use Aigisu\Api\Middlewares\Access\ModeratorAccessMiddleware;
use Aigisu\Api\Middlewares\Access\OwnerAccessMiddleware;
use Aigisu\Api\Middlewares\Base64FileMiddleware;
use Aigisu\Api\Middlewares\CG\ExtendedServerExceptionHandler;
use Aigisu\Api\Middlewares\ParserUnitTagsMiddleware;
use Aigisu\Api\Middlewares\Validators\CreateCGValidator;
use Aigisu\Api\Middlewares\Validators\CreateUnitValidator;
use Aigisu\Api\Middlewares\Validators\CreateUserValidator;
use Aigisu\Api\Middlewares\Validators\MissingCGValidator;
use Aigisu\Api\Middlewares\Validators\UpdateCGValidator;
use Aigisu\Api\Middlewares\Validators\UpdateUnitValidator;
use Aigisu\Api\Middlewares\Validators\UpdateUserValidator;
use League\OAuth2\Server\Middleware\ResourceServerMiddleware;
use League\OAuth2\Server\ResourceServer;

/** @var $this \Aigisu\Core\Main */
$this->group('/users', function () {
    /** @var $this \Aigisu\Core\Main */
    $this->get('', UserController::class . ':actionIndex')
        ->setName('api.user.index');

    $this->get('/{id:\d+}', UserController::class . ':actionView')
        ->setName('api.user.view');

    $this->group('', function () {
        /** @var $this \Aigisu\Core\Main */
        $this->post('', UserController::class . ':actionCreate')
            ->setName('api.user.create')
            ->add(new CreateUserValidator($this->getContainer()));

        $this->patch('/{id:\d+}', UserController::class . ':actionUpdate')
            ->setName('api.user.update')
            ->add(new UpdateUserValidator($this->getContainer()));

        $this->delete('/{id:\d+}', UserController::class . ':actionDelete')
            ->setName('api.user.delete');

    })->add(new OwnerAccessMiddleware($this->getContainer()));

})->add(new ResourceServerMiddleware($this->getContainer()->get(ResourceServer::class)));

$this->group('/units', function () {
    /** @var $this \Aigisu\Core\Main */
    $this->group('', function () {
        /** @var $this \Aigisu\Core\Main */
        $this->post('', UnitController::class . ':actionCreate')
            ->setName('api.unit.create')
            ->add(new CreateUnitValidator($this->getContainer()))
            ->add(new ParserUnitTagsMiddleware($this->getContainer()));

        $this->post('/{id:\d+}', UnitController::class . ':actionUpdate')
            ->setName('api.unit.update')
            ->add(new UpdateUnitValidator($this->getContainer()))
            ->add(new ParserUnitTagsMiddleware($this->getContainer()));

        $this->delete('/{id:\d+}', UnitController::class . ':actionDelete')
            ->setName('api.unit.delete');

    })
        ->add(new AdminAccessMiddleware($this->getContainer()))
        ->add(new ResourceServerMiddleware($this->getContainer()->get(ResourceServer::class)));

    $this->get('', UnitController::class . ':actionIndex')
        ->setName('api.unit.index');

    $this->get('/{id:\d+}', UnitController::class . ':actionView')
        ->setName('api.unit.view');

    $this->get('/rarities', UnitController::class . ':actionRarities')
        ->setName('api.unit.rarities');

    $this->group('/{unitId:\d+}/cg', function () {
        /** @var $this \Aigisu\Core\Main */
        $this->group('', function () {
            /** @var $this \Aigisu\Core\Main */
            $this->post('', CGController::class . ':actionCreate')
                ->setName('api.unit.cg.create')
                ->add(new MissingCGValidator($this->getContainer()))
                ->add(new CreateCGValidator($this->getContainer()));

            $this->post('/{id:\d+}', CGController::class . ':actionUpdate')
                ->setName('api.unit.cg.update')
                ->add(new MissingCGValidator($this->getContainer()))
                ->add(new UpdateCGValidator($this->getContainer()));

            $this->group('/{id:\d+}/google', function () {
                /** @var $this \Aigisu\Core\Main */
                $this->post('', GoogleUploader::class . ':actionCreate')
                    ->setName('api.unit.cg.google.create');

                $this->patch('', GoogleUploader::class . ':actionUpdate')
                    ->setName('api.unit.cg.google.update');

                $this->delete('', GoogleUploader::class . ':actionDelete')
                    ->setName('api.unit.cg.google.delete');

            })->add(new ExtendedServerExceptionHandler($this->getContainer()));

            $this->group('/{id:\d+}/imgur', function () {
                $this->post('', ImgurUploader::class . ':actionCreate')
                    ->setName('api.unit.cg.imgur.create');

                $this->patch('', ImgurUploader::class . ':actionUpdate')
                    ->setName('api.unit.cg.imgur.update');

                $this->delete('', ImgurUploader::class . ':actionDelete')
                    ->setName('api.unit.cg.imgur.delete');

            })->add(new ExtendedServerExceptionHandler($this->getContainer()));

            $this->delete('/{id:\d+}', CGController::class . ':actionDelete')
                ->setName('api.unit.cg.delete');

        })->add(new ModeratorAccessMiddleware($this->getContainer()))
            ->add(new ResourceServerMiddleware($this->getContainer()->get(ResourceServer::class)));

        $this->get('', CGController::class . ':actionIndex')
            ->setName('api.unit.cg.index');

        $this->get('/{id:\d+}', CGController::class . ':actionView')
            ->setName('api.unit.cg.view');

    });
});

$this->post('/auth', AuthController::class . ':actionCreate');
$this->add(new Base64FileMiddleware($this->getContainer()));