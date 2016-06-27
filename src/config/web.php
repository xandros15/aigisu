<?php

use Controllers\ImageController;
use Controllers\ImageFileController;
use Controllers\OauthController;
use Controllers\UnitController;

return [
    'siteUrl' => 'http://aigisu.pl',
    'locale' => 'en',
    'controllers' => [
        ImageController::class,
        ImageFileController::class,
        UnitController::class,
        OauthController::class,
    ],
];
