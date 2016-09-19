<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 22:26
 */
use Aigisu\Common\Components\View\CallbackManager;
use Aigisu\Common\Components\View\LayoutExtension;
use Aigisu\Common\Components\View\UrlExtension;
use Aigisu\Common\Components\View\View;
use Aigisu\Components\Http\Filesystem\FilesystemManager;
use Interop\Container\ContainerInterface;

return [
    'filesystems' => function (ContainerInterface $container) {
        return require 'filesystems.php';
    },
    View::class => function (ContainerInterface $container) {
        $callbackManager = new CallbackManager();
        $callbackManager->addClassCallbacks(new UrlExtension($container));
        $callbackManager->addClassCallbacks(new LayoutExtension());
        return new View($container->get('viewPath'), $callbackManager);
    },
    FilesystemManager::class => function (ContainerInterface $container) {
        return new FilesystemManager($container);
    }
];