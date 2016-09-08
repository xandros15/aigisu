<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-08
 * Time: 00:29
 */
use Aigisu\Api\Middlewares\ExceptionHandler;
use Aigisu\Common\Middlewares\Alert;
use Aigisu\Common\Middlewares\HomeAssets;
use Aigisu\Common\Middlewares\ShowQueries;
use Aigisu\Common\Middlewares\TrailingSlash;
use Aigisu\Common\Middlewares\View;
use Interop\Container\ContainerInterface;

/** @var ContainerInterface $container */
return [
    TrailingSlash::class => new TrailingSlash($container),
    HomeAssets::class => new HomeAssets($container),
    ShowQueries::class => new ShowQueries($container),
    View::class => new View($container),
    Alert::class => new Alert($container),
    ExceptionHandler::class => new ExceptionHandler($container),
];