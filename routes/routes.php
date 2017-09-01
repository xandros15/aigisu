<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-26
 * Time: 22:16
 */

use Aigisu\Components\AccessControlAllowMiddleware;
use Aigisu\Components\Auth\IsGuestMiddleware;
use Aigisu\Components\Auth\JWTAuthMiddleware;
use Aigisu\Components\Base64FileMiddleware;
use Aigisu\Components\Http\MiddlewareHandler;
use Aigisu\Components\Http\UploadedFilesMiddleware;
use Aigisu\Components\ModelNotFoundHandlerMiddleware;

/** @var $main \Slim\App */
$api = $main->group('', function () {
    require __DIR__ . '/api.php';
});

$api->add(new Base64FileMiddleware());
$api->add(new JWTAuthMiddleware($main->getContainer()));
$api->add(new AccessControlAllowMiddleware());

$main->add(new ModelNotFoundHandlerMiddleware());
$main->add(new UploadedFilesMiddleware($main->getContainer()));
$main->add(new MiddlewareHandler($main->getContainer()));
$main->add(new IsGuestMiddleware());
