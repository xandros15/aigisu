<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 21:43
 */
use Aigisu\Core\Configuration;

return [
    'root' => Configuration::DIR_ROOT,
    'web' => Configuration::DIR_WEB,
    'locale' => 'en',
    'templates' => function (Configuration $container) {
        return "{$container->root}/templates";
    },
    'routes' => function (Configuration $container) {
        return "{$container->root}/routes";
    },
    'cache' => function (Configuration $container) {
        return "{$container->root}/cache";
    },
    'storage' => function (Configuration $container) {
        return "{$container->root}/storage";
    },
    'upload' => function (Configuration $container) {
        return "{$container->storage}/app";
    },
    'public' => function (Configuration $container) {
        return "{$container->upload}/public";
    },
    'siteUrl' => function (Configuration $container) {
        return rtrim($container->get('request')->getUri()->getBaseUrl(), '/');
    },
    'sprite.icons' => function (Configuration $container) {
        return "{$container->public}/sprite/icons";
    },
    'middlewares' => function () {
        return require __DIR__ . '/middlewares.php';
    },
    'database' => function () {
        return require __DIR__ . '/db/params.php';
    },
    'access' => function () {
        return require __DIR__ . '/access.php';
    },
    'isDebug' => function (Configuration $container) {
        return $container->get('settings')->get('displayErrorDetails');
    },
    'auth' => function () {
        return require __DIR__ . '/auth.php';
    },
];
