<?php

/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 22:17
 */
use Aigisu\Core\Configuration;

/** @var Configuration $container */
return [
    'default' => 'public',
    'cloud' => 'public',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => $container['upload'],
        ],

        'public' => [
            'driver' => 'local',
            'root' => "{$container['upload']}/public",
            'visibility' => 'public',
        ],
    ],
];
