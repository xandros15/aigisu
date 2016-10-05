<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-05
 * Time: 19:13
 */
use Aigisu\Api\Middlewares\ExceptionHandler;
use Aigisu\Api\Middlewares\UrlManagerModelAccess;
use Aigisu\Common\Middlewares\Alert;
use Aigisu\Common\Middlewares\HomeAssets;
use Aigisu\Common\Middlewares\ShowQueries;
use Aigisu\Common\Middlewares\TrailingSlash;

return [
    'web' => [
        TrailingSlash::class,
        HomeAssets::class,
        ShowQueries::class,
        Alert::class,
    ],
    'api' => [
        ExceptionHandler::class,
        UrlManagerModelAccess::class,
    ],
    'storage' => [],
];