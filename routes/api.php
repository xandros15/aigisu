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
use Aigisu\Components\Auth\JWTAuthMiddleware;
use Aigisu\Middlewares\Access\AdminAccessMiddleware;
use Aigisu\Middlewares\Access\ModeratorAccessMiddleware;
use Aigisu\Middlewares\Access\OwnerAccessMiddleware;
use Aigisu\Middlewares\AccessControlAllowMiddleware;
use Aigisu\Middlewares\Base64FileMiddleware;
use Aigisu\Middlewares\CG\ExtendedServerExceptionHandler;
use Aigisu\Middlewares\MissingCGValidatorMiddleware;
use Aigisu\Middlewares\ParserUnitTagsMiddleware;
use Aigisu\Middlewares\ValidatorMiddleware;

/** @var $this \Slim\App */
$this->group('/users', function () {
    /** @var $this \Slim\App */
    $this->get('', UserController::class . ':actionIndex')
        ->setName('api.user.index');

    $this->get('/{id:\d+}', UserController::class . ':actionView')
        ->setName('api.user.view');


    $this->get('/current', UserController::class . ':actionGetCurrent')
        ->setName('api.user.current');

    $this->group('', function () {
        /** @var $this \Slim\App */
        $this->post('', UserController::class . ':actionCreate')
            ->setName('api.user.create')
            ->add(new ValidatorMiddleware($this->getContainer(), 'user.create'));

        $this->post('/{id:\d+}', UserController::class . ':actionUpdate')
            ->setName('api.user.update')
            ->add(new ValidatorMiddleware($this->getContainer(), 'user.update'));

        $this->delete('/{id:\d+}', UserController::class . ':actionDelete')
            ->setName('api.user.delete');

    })->add(new OwnerAccessMiddleware($this->getContainer()));

})->add(new ModeratorAccessMiddleware($this->getContainer()));

$this->group('/units', function () {
    /** @var $this \Slim\App */
    $this->group('', function () {
        /** @var $this \Slim\App */
        $this->group('', function () {
            $this->post('', UnitController::class . ':actionCreate')
                ->setName('api.unit.create')
                ->add(new ValidatorMiddleware($this->getContainer(), 'unit.create'));

            $this->post('/{id:\d+}', UnitController::class . ':actionUpdate')
                ->setName('api.unit.update')
                ->add(new ValidatorMiddleware($this->getContainer(), 'unit.update'));
        })->add(new ParserUnitTagsMiddleware());

        $this->delete('/{id:\d+}', UnitController::class . ':actionDelete')
            ->setName('api.unit.delete');

    })->add(new AdminAccessMiddleware($this->getContainer()));

    $this->get('', UnitController::class . ':actionIndex')
        ->setName('api.unit.index');

    $this->get('/{id:\d+}', UnitController::class . ':actionView')
        ->setName('api.unit.view');

    $this->get('/rarities', UnitController::class . ':actionRarities')
        ->setName('api.unit.rarities');

    $this->group('/{unitId:\d+}/cg', function () {
        /** @var $this \Slim\App */
        $this->group('', function () {
            /** @var $this \Slim\App */

            $this->group('', function () {
                $this->post('', CGController::class . ':actionCreate')
                    ->setName('api.unit.cg.create')
                    ->add(new ValidatorMiddleware($this->getContainer(), 'cg.create'));

                $this->post('/{id:\d+}', CGController::class . ':actionUpdate')
                    ->setName('api.unit.cg.update')
                    ->add(new ValidatorMiddleware($this->getContainer(), 'cg.update'));
            })->add(new MissingCGValidatorMiddleware());

            $this->group('', function () {
                $this->group('/{id:\d+}/google', function () {
                    /** @var $this \Slim\App */
                    $this->post('', GoogleUploader::class . ':actionCreate')
                        ->setName('api.unit.cg.google.create');

                    $this->patch('', GoogleUploader::class . ':actionUpdate')
                        ->setName('api.unit.cg.google.update');

                    $this->delete('', GoogleUploader::class . ':actionDelete')
                        ->setName('api.unit.cg.google.delete');

                });
                $this->group('/{id:\d+}/imgur', function () {
                    $this->post('', ImgurUploader::class . ':actionCreate')
                        ->setName('api.unit.cg.imgur.create');

                    $this->patch('', ImgurUploader::class . ':actionUpdate')
                        ->setName('api.unit.cg.imgur.update');

                    $this->delete('', ImgurUploader::class . ':actionDelete')
                        ->setName('api.unit.cg.imgur.delete');

                });
            })->add(new ExtendedServerExceptionHandler());

            $this->delete('/{id:\d+}', CGController::class . ':actionDelete')
                ->setName('api.unit.cg.delete');

        })->add(new ModeratorAccessMiddleware($this->getContainer()));

        $this->get('', CGController::class . ':actionIndex')
            ->setName('api.unit.cg.index');

        $this->get('/{id:\d+}', CGController::class . ':actionView')
            ->setName('api.unit.cg.view');

    });
});

$this->post('/auth', AuthController::class . ':actionCreate');

$this->add(new Base64FileMiddleware());
$this->add(new JWTAuthMiddleware($this->getContainer()));
$this->add(new AccessControlAllowMiddleware());
