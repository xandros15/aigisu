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
use Aigisu\Middlewares\AccessControlAllowMiddleware;
use Aigisu\Middlewares\Base64FileMiddleware;
use Aigisu\Middlewares\CG\ExtendedServerExceptionHandler;
use Aigisu\Middlewares\MissingCGValidatorMiddleware;
use Aigisu\Middlewares\ParserUnitTagsMiddleware;
use Aigisu\Middlewares\ValidatorMiddleware;

/** @var $this \Slim\App */
/** @var $acl AccessManager */
$acl = $this->getContainer()->get(AccessManager::class);


/** @var $this \Slim\App */
$this->group('', function () {
    $this->get('/users', UserController::class . ':actionIndex')->setName('api.user.index');
    $this->get('/users/{id:\d+}', UserController::class . ':actionView')->setName('api.user.view');
    $this->get('/users/current', UserController::class . ':actionGetCurrent')->setName('api.user.current');
})->add($acl->get('moderator'));

$this->group('', function () {
    $this->post('/users', UserController::class . ':actionCreate')
        ->setName('api.user.create')
        ->add(new ValidatorMiddleware($this->getContainer(), 'user.create'));
    $this->post('/users/{id:\d+}', UserController::class . ':actionUpdate')
        ->setName('api.user.update')
        ->add(new ValidatorMiddleware($this->getContainer(), 'user.update'));
    $this->delete('/users/{id:\d+}', UserController::class . ':actionDelete')
        ->setName('api.user.delete');
})->add($acl->get('owner'));

$this->group('', function () {
    $tagsParser = new ParserUnitTagsMiddleware();
    $this->post('/units', UnitController::class . ':actionCreate')
        ->setName('api.unit.create')
        ->add(new ValidatorMiddleware($this->getContainer(), 'unit.create'))
        ->add($tagsParser);
    $this->post('/units/{id:\d+}', UnitController::class . ':actionUpdate')
        ->setName('api.unit.update')
        ->add(new ValidatorMiddleware($this->getContainer(), 'unit.update'))
        ->add($tagsParser);
    $this->delete('/units/{id:\d+}', UnitController::class . ':actionDelete')
        ->setName('api.unit.delete');
})->add($acl->get('admin'));

$this->get('/units', UnitController::class . ':actionIndex')->setName('api.unit.index');
$this->get('/units/{id:\d+}', UnitController::class . ':actionView')->setName('api.unit.view');
$this->get('/units/rarities', UnitController::class . ':actionRarities')->setName('api.unit.rarities');


$this->group('', function () {
    $missingCG = new MissingCGValidatorMiddleware();
    $this->post('/units/{unitId:\d+}/cg', CGController::class . ':actionCreate')
        ->setName('api.unit.cg.create')
        ->add(new ValidatorMiddleware($this->getContainer(), 'cg.create'))
        ->add($missingCG);
    $this->post('/units/{unitId:\d+}/cg/{id:\d+}', CGController::class . ':actionUpdate')
        ->setName('api.unit.cg.update')
        ->add(new ValidatorMiddleware($this->getContainer(), 'cg.update'))
        ->add($missingCG);
    $this->delete('/units/{unitId:\d+}/cg/{id:\d+}', CGController::class . ':actionDelete')
        ->setName('api.unit.cg.delete');
})->add($acl->get('moderator'));

$this->group('', function () {
    /** @var $this \Slim\App */
    $this->post('/units/{unitId:\d+}/cg/{id:\d+}/google', GoogleUploader::class . ':actionCreate')
        ->setName('api.unit.cg.google.create');
    $this->patch('/units/{unitId:\d+}/cg/{id:\d+}/google', GoogleUploader::class . ':actionUpdate')
        ->setName('api.unit.cg.google.update');
    $this->delete('/units/{unitId:\d+}/cg/{id:\d+}/google', GoogleUploader::class . ':actionDelete')
        ->setName('api.unit.cg.google.delete');
    $this->post('/units/{unitId:\d+}/cg/{id:\d+}/imgur', ImgurUploader::class . ':actionCreate')
        ->setName('api.unit.cg.imgur.create');
    $this->patch('/units/{unitId:\d+}/cg/{id:\d+}/imgur', ImgurUploader::class . ':actionUpdate')
        ->setName('api.unit.cg.imgur.update');
    $this->delete('/units/{unitId:\d+}/cg/{id:\d+}/imgur', ImgurUploader::class . ':actionDelete')
        ->setName('api.unit.cg.imgur.delete');
})->add(new ExtendedServerExceptionHandler())
    ->add($acl->get('moderator'));

$this->get('/units/{unitId:\d+}/cg', CGController::class . ':actionIndex')
    ->setName('api.unit.cg.index');
$this->get('/units/{unitId:\d+}/cg/{id:\d+}', CGController::class . ':actionView')
    ->setName('api.unit.cg.view');

$this->post('/auth', AuthController::class . ':actionCreate');

$this->add(new Base64FileMiddleware());
$this->add(new JWTAuthMiddleware($this->getContainer()));
$this->add(new AccessControlAllowMiddleware());
