<?php
//silence is golden

/** @var $this \Slim\App */

use Aigisu\Components\Auth\SessionAuthMiddleware;
use Aigisu\Web\Controllers\AbstractController;
use Aigisu\Web\Controllers\AuthController;
use Aigisu\Web\Controllers\SiteController;
use Aigisu\Web\Controllers\UnitController;

$this->get('[/]', function () {
    list(, $response) = func_get_args();
    /** @var $response \Slim\Http\Response */
    return $response->withRedirect('/units');
})->setName(AbstractController::HOME_PATH_NAME);

$this->get('/units', UnitController::class . ':actionIndex')->setName('web.units');

$this->get('/auth', AuthController::class . ':actionView');
$this->post('/auth', AuthController::class . ':actionSignin')->setName('web.auth.signin');
$this->get('/auth/signout', AuthController::class . ':actionSignout');

$this->post('/register', SiteController::class . ':actionRegister')->setName('web.site.register');
$this->get('/register', SiteController::class . ':actionRegisterView')->setName('web.site.register.view');

$this->add(new SessionAuthMiddleware());
