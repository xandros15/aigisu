<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-26
 * Time: 22:16
 */

use Aigisu\Components\Auth\IsGuestMiddleware;
use Aigisu\Components\Auth\JWTAuthMiddleware;
use Aigisu\Components\Auth\SessionAuthMiddleware;
use Aigisu\Components\Auth\TwigAuthMiddleware;
use Aigisu\Components\Http\MiddlewareHandler;
use Aigisu\Components\Http\UploadedFilesMiddleware;
use Aigisu\Middlewares\AccessControlAllowMiddleware;
use Aigisu\Middlewares\Base64FileMiddleware;
use Aigisu\Middlewares\ModelNotFoundHandlerMiddleware;

/** @var $main \Slim\App */
$web = $main->group('', function () {
    require __DIR__ . '/web.php';
});

$web->add(new TwigAuthMiddleware($main->getContainer()));
$web->add(new SessionAuthMiddleware());

$api = $main->group('/api', function () {
    require __DIR__ . '/api.php';
});

$api->add(new Base64FileMiddleware());
$api->add(new JWTAuthMiddleware($main->getContainer()));
$api->add(new AccessControlAllowMiddleware());

$main->group('/storage', function () {
    require __DIR__ . '/storage.php';
});

$main->add(new ModelNotFoundHandlerMiddleware());
$main->add(new UploadedFilesMiddleware($main->getContainer()));
$main->add(new MiddlewareHandler($main->getContainer()));
$main->add(new IsGuestMiddleware());
