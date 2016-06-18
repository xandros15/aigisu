<?php

use controller\ImageController;
use controller\ImageFileController;
use controller\UnitController;
use controller\OauthController;

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
