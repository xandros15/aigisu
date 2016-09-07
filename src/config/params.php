<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 21:43
 */
use Aigisu\Components\Http\Filesystem\FilesystemManager;
use Aigisu\Core\Configuration;
use Aigisu\Helpers\Filesystem;

return [
    'root' => Filesystem::resolvePath(Configuration::DIR_ROOT),
    'web' => Filesystem::resolvePath(Configuration::DIR_WEB),
    'locale' => 'en',
    'storage' => function (Configuration $container) {
        return Filesystem::resolvePath("{$container->root}/storage");
    },
    'upload' => function (Configuration $container) {
        return Filesystem::resolvePath("{$container->storage}/app");
    },
    'public' => function (Configuration $container) {
        return Filesystem::resolvePath("{$container->upload}/public");
    },
    'siteUrl' => function (Configuration $container) {
        return rtrim($container->get('request')->getUri()->withPath('')->withQuery('')->withFragment(''), '/');
    },
    FilesystemManager::class => function (Configuration $container) {
        return new FilesystemManager($container);
    }
];