<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-30
 * Time: 18:32
 */

use Google_Service_Drive as GoogleDrive;

return [
    'client' => [
        'application_name' => 'aigisu',
        'access-type' => 'offline',
        'access-file' => __DIR__ . '/google/credentials.v3.json',
        'auth' => __DIR__ . '/google/key.json',
        'scopes' => [
            GoogleDrive::DRIVE_METADATA,
            GoogleDrive::DRIVE_FILE
        ],
    ],
    'drive' => [
        'rootId' => require __DIR__ . '/google/root.php',
    ]
];
