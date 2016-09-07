<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-05
 * Time: 22:26
 */
use Aigisu\Core\Configuration;

return [
    'filesystems' => function (Configuration $configuration) {
        return require 'filesystems.php';
    },
];