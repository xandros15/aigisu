<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-26
 * Time: 22:16
 */

use Aigisu\Components\Auth\IsGuestMiddleware;
use Aigisu\Components\Http\UploadedFilesMiddleware;
use Aigisu\Middlewares\ModelNotFoundHandlerMiddleware;

/** @var $main \Slim\App */
$main->group('', function () {
    require __DIR__ . '/web.php';
});

$main->group('/api', function () {
    require __DIR__ . '/api.php';
});

$main->group('/storage', function () {
    require __DIR__ . '/storage.php';
});

$main->add(new ModelNotFoundHandlerMiddleware());
$main->add(new UploadedFilesMiddleware($main->getContainer()));
$main->add(new IsGuestMiddleware());