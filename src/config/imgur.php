<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-04
 * Time: 13:35
 */
return [
    'client' => [
        'auth' => require __DIR__ . '/imgur/key.php',
        'access-file' => __DIR__ . '/imgur/credentials.json'
    ],
    'albums' => require __DIR__ . '/imgur/albums.php',
];