<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-09
 * Time: 15:48
 */

use Aigisu\Middlewares\Access\AdminAccessMiddleware;
use Aigisu\Middlewares\Access\ModeratorAccessMiddleware;
use Aigisu\Middlewares\Access\OwnerAccessMiddleware;

return [
    [
        'role' => 'owner',
        'level' => 0,
        'class' => OwnerAccessMiddleware::class,
    ],
    [
        'role' => 'admin',
        'level' => 1,
        'class' => AdminAccessMiddleware::class,
    ],
    [
        'role' => 'moderator',
        'level' => 2,
        'class' => ModeratorAccessMiddleware::class,
    ],
];
