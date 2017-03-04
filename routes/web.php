<?php
//silence is golden

/** @var $this \Slim\App */

use Aigisu\Components\Auth\SessionAuthMiddleware;
use Aigisu\Components\Auth\TwigAuthMiddleware;
use Aigisu\Components\CloneFlashMiddleware;
use Aigisu\Components\Http\MiddlewareHandler;
use Aigisu\Web\Controllers\AdminController;
use Aigisu\Web\Controllers\SiteController;
use Aigisu\Web\Controllers\UnitController;

$this->get('[/]', function () {
    list(, $response) = func_get_args();
    /** @var $response \Slim\Http\Response */
    return $response->withRedirect('/units');
})->add(new CloneFlashMiddleware($this->getContainer()))->setName('web.home');

$this->get('/units', UnitController::class . ':actionIndex')->setName('web.units');

$this->map(['get', 'post'], '/signin', SiteController::class . ':actionSignin')->setName('web.site.signin');
$this->get('/signout', SiteController::class . ':actionSignout')->setName('web.site.signout');

$this->map(['post', 'get'], '/signup', SiteController::class . ':actionRegister')->setName('web.site.signup');

$this->get('/admin/users', AdminController::class . ':actionIndexUsers')->setName('web.admin.user.index');
$this->get('/admin/users/{id:\d+}/activate', AdminController::class . ':actionActivateUser')
    ->setName('web.admin.activate');
$this->get('/admin/users/{id:\d+}/deactivate', AdminController::class . ':actionDeactivateUser')
    ->setName('web.admin.deactivate');

$this->map(['get', 'post'], '/admin/users/{id:\d+}', AdminController::class . ':actionUpdateUser')
    ->setName('web.admin.user.update');

$this->map(['post', 'get'], '/password/reset/send', SiteController::class . ':actionPasswordResetRequest')
    ->setName('web.user.password.reset.send');

$this->map(['post', 'get'], '/password/reset', SiteController::class . ':actionPasswordReset')
    ->setName('web.user.password.reset');


$this->add(new MiddlewareHandler($this->getContainer()));
$this->add(new TwigAuthMiddleware($this->getContainer()));
$this->add(new SessionAuthMiddleware());
