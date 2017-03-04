<?php
//silence is golden

/** @var $this \Slim\App */

use Aigisu\Components\Auth\SessionAuthMiddleware;
use Aigisu\Components\Auth\TwigAuthMiddleware;
use Aigisu\Components\Http\MiddlewareHandler;
use Aigisu\Web\Controllers\AbstractController;
use Aigisu\Web\Controllers\AdminController;
use Aigisu\Web\Controllers\SiteController;
use Aigisu\Web\Controllers\UnitController;

$this->get('[/]', function () {
    list(, $response) = func_get_args();
    /** @var $response \Slim\Http\Response */
    return $response->withRedirect('/units');
})->setName(AbstractController::HOME_PATH_NAME);

$this->get('/units', UnitController::class . ':actionIndex')->setName('web.units');

$this->post('/signin', SiteController::class . ':actionSignin')->setName('web.site.signin');
$this->get('/signin', SiteController::class . ':actionView')->setName('web.site.signin.view');
$this->get('/signout', SiteController::class . ':actionSignout')->setName('web.site.signout');

$this->post('/signup', SiteController::class . ':actionRegister')->setName('web.site.signup');
$this->get('/signup', SiteController::class . ':actionRegisterView')->setName('web.site.signup.view');

$this->get('/admin/users', AdminController::class . ':actionIndexUsers');
$this->get('/admin/users/{id:\d+}/activate', AdminController::class . ':actionActivateUser')
    ->setName('web.admin.activate');
$this->get('/admin/users/{id:\d+}/deactivate', AdminController::class . ':actionDeactivateUser')
    ->setName('web.admin.deactivate');


$this->add(new MiddlewareHandler($this->getContainer()));
$this->add(new TwigAuthMiddleware($this->getContainer()));
$this->add(new SessionAuthMiddleware());
