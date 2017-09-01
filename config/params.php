<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 21:43
 */


defined('ROOT') || define('ROOT', dirname(dirname(__FILE__)));
defined('DEBUG') || define('DEBUG', true);

return [
    'root' => ROOT,
    'web' => ROOT . '/web',
    'debug' => DEBUG,
    'locale' => 'en',
    'app.name' => 'Aigisu',
    'settings' => [
        'access' => require ROOT . '/config/access.php',
        'imgur' => require ROOT . '/config/imgur.php',
        'google' => require ROOT . '/config/google.php',
        'auth' => require ROOT . '/config/auth.php',
        'database' => require ROOT . '/config/db/params.php',
        'mailer' => require ROOT . '/config/mailer/mailer.php',
        'flysystem' => [
            'local' => ROOT . '/storage/app',
        ],
        'twig' => [
            'cache' => DEBUG ? false : ROOT . '/cache',
            'templates' => ROOT . '/templates',
            'debug' => DEBUG,
        ],
        'routerCacheFile' => DEBUG ? false : ROOT . '/cache/route.cache.php',
        'displayErrorDetails' => DEBUG,
        'addContentLengthHeader' => true,
        'determineRouteBeforeAppMiddleware' => true,
    ],
];
