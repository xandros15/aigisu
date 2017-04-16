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
use Aigisu\Components\ACL\AccessManager;
use Aigisu\Components\Auth\JWTAuthMiddleware;
use Aigisu\Components\Validators\ValidatorManager;
use Aigisu\Middlewares\AccessControlAllowMiddleware;
use Aigisu\Middlewares\Base64FileMiddleware;
use Aigisu\Middlewares\CG\ExtendedServerExceptionHandler;
use Aigisu\Middlewares\MissingCGValidatorMiddleware;
use Aigisu\Middlewares\ParserUnitTagsMiddleware;
use Aigisu\Middlewares\ValidatorMiddleware;

/** @var $this \Slim\App */
/** @var $acl AccessManager */
$acl = $this->getContainer()->get(AccessManager::class);
/** @var $validators ValidatorManager */
$validators = $this->getContainer()->get(ValidatorManager::class);

$this->post('/users', UserController::class . ':actionRegister')
     ->setName('api.user.create')->add(new ValidatorMiddleware($validators->get('user.create')));
$this->post('/users/password/reset/send', UserController::class . ':actionResetPasswordRequest')
     ->add(new ValidatorMiddleware($validators->get('user.password.reset.request')))
     ->setName('api.user.password.reset.send');
$this->post('/users/password/reset/{token:\w+}', UserController::class . ':actionResetPassword')
     ->add(new ValidatorMiddleware($validators->get('user.password.reset')))
     ->setName('api.user.password.reset');

$this->group('', function () {
    $this->get('/users', UserController::class . ':actionIndex')->setName('api.user.index');
    $this->get('/users/{id:\d+}', UserController::class . ':actionView')->setName('api.user.view');
    $this->get('/users/current', UserController::class . ':actionGetCurrent')->setName('api.user.current');
})->add($acl->get('moderator'));

$this->group('', function () use ($validators) {
    $this->post('/users/{id:\d+}', UserController::class . ':actionUpdate')
         ->setName('api.user.update')->add(new ValidatorMiddleware($validators->get('user.update')));
    $this->post('/users/{id:\d+}/activate', UserController::class . ':actionActivate')
         ->setName('api.user.activate');
    $this->post('/users/{id:\d+}/deactivate', UserController::class . ':actionDeactivate')
         ->setName('api.user.deactivate');
    $this->post('/users/{id:\d+}/role', UserController::class . ':actionChangeRole')
         ->setName('api.user.role')
         ->add(new ValidatorMiddleware($validators->get('user.role')));
    $this->delete('/users/{id:\d+}', UserController::class . ':actionDelete')
         ->setName('api.user.delete');
})->add($acl->get('owner'));

$this->group('', function () use ($validators) {
    $tagsParser = new ParserUnitTagsMiddleware();
    $this->post('/units', UnitController::class . ':actionCreate')
         ->setName('api.unit.create')
         ->add(new ValidatorMiddleware($validators->get('unit.create')))
         ->add($tagsParser);
    $this->post('/units/{id:\d+}', UnitController::class . ':actionUpdate')
         ->setName('api.unit.update')
         ->add(new ValidatorMiddleware($validators->get('unit.update')))
         ->add($tagsParser);
    $this->delete('/units/{id:\d+}', UnitController::class . ':actionDelete')
         ->setName('api.unit.delete');
})->add($acl->get('admin'));

$this->get('/units', UnitController::class . ':actionIndex')->setName('api.unit.index');
$this->get('/units/{id:\d+}', UnitController::class . ':actionView')->setName('api.unit.view');
$this->get('/units/rarities', UnitController::class . ':actionRarities')->setName('api.unit.rarities');

$this->group('', function () use ($validators) {
    $missingCG = new MissingCGValidatorMiddleware();
    $this->post('/cg', CGController::class . ':actionCreate')
         ->setName('api.unit.cg.create')
         ->add($missingCG)
         ->add(new ValidatorMiddleware($validators->get('cg.create')));
    $this->post('/cg/{id:\d+}', CGController::class . ':actionUpdate')
         ->setName('api.unit.cg.update')
         ->add($missingCG)
         ->add(new ValidatorMiddleware($validators->get('cg.update')));
    $this->delete('/cg/{id:\d+}', CGController::class . ':actionDelete')
         ->setName('api.unit.cg.delete');
})->add($acl->get('moderator'));

$this->group('', function () {
    /** @var $this \Slim\App */
    $this->post('/cg/{id:\d+}/google', GoogleUploader::class . ':actionCreate')
         ->setName('api.unit.cg.google.create');
    $this->patch('/cg/{id:\d+}/google', GoogleUploader::class . ':actionUpdate')
         ->setName('api.unit.cg.google.update');
    $this->delete('/cg/{id:\d+}/google', GoogleUploader::class . ':actionDelete')
         ->setName('api.unit.cg.google.delete');
    $this->post('/cg/{id:\d+}/imgur', ImgurUploader::class . ':actionCreate')
         ->setName('api.unit.cg.imgur.create');
    $this->patch('/cg/{id:\d+}/imgur', ImgurUploader::class . ':actionUpdate')
         ->setName('api.unit.cg.imgur.update');
    $this->delete('/cg/{id:\d+}/imgur', ImgurUploader::class . ':actionDelete')
         ->setName('api.unit.cg.imgur.delete');
})->add(new ExtendedServerExceptionHandler())
     ->add($acl->get('moderator'));

$this->get('/units/{unitId:\d+}/cg', CGController::class . ':actionIndex')
     ->setName('api.unit.cg.index');
$this->get('/cg/{id:\d+}', CGController::class . ':actionView')
     ->setName('api.unit.cg.view');

$this->post('/auth', AuthController::class . ':actionCreate');

$this->add(new Base64FileMiddleware());
$this->add(new JWTAuthMiddleware($this->getContainer()));
$this->add(new AccessControlAllowMiddleware());
