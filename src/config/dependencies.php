<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 22:26
 */
use Aigisu\Core\MiddlewareProvider;
use Interop\Container\ContainerInterface;

return [
    'filesystems' => function (ContainerInterface $container) {
        return require 'filesystems.php';
    },
    'middlewares' => function (ContainerInterface $container) {
        return new MiddlewareProvider($container);
    },
];