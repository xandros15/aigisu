<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-05
 * Time: 19:13
 */
use Aigisu\Api\Middlewares\ExceptionHandler;
use Aigisu\Api\Middlewares\UrlManagerModelAccess;

return [
    'web' => [],
    'api' => [
        ExceptionHandler::class,
        UrlManagerModelAccess::class,
    ],
    'storage' => [],
];