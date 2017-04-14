<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-01-21
 * Time: 01:25
 */

return [
    'settings' => [
        'routerCacheFile'                   => $this->debug ? false : $this->root . '/cache/route.cache.php',
        'displayErrorDetails'               => $this->debug,
        'addContentLengthHeader'            => false,
        'determineRouteBeforeAppMiddleware' => true,
    ],
];
