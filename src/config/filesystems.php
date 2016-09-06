<?php

/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 22:17
 */
use Aigisu\Helpers\Filesystem;

return [
    'default' => 'public',
    'cloud' => 'public',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => $this['upload'],
        ],

        'public' => [
            'driver' => 'local',
            'root' => Filesystem::resolvePath("{$this['upload']}/public"),
            'visibility' => 'public',
        ],
    ],
];