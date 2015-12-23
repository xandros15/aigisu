<?php

use controller\ImageController;
use controller\ImageFileController;
use controller\UnitController;
use controller\OauthController;

return [
    'siteUrl' => 'http://aigisu.pl/',
    'locale' => 'en',
    'debug' => false,
    'slim' => [
        'values' => [
            ImageController::class => function ($container) {
                return new ImageController($container);
            },
            ImageFileController::class => function ($container) {
                return new ImageFileController($container);
            },
            UnitController::class => function ($container) {
                return new UnitController($container);
            },
            OauthController::class => function ($container) {
                return new OauthController($container);
            },
            'settings' => [
                'displayErrorDetails' => true,
            ],
        ],
        'rules' => [
            '/' => [
                'methods' => ['get'],
                'action' => UnitController::class . ':actionIndex',
                'name' => 'home'
            ],
            '/image' => [
                '/upload/{id:\d+}' => [
                    'methods' => ['post'],
                    'action' => ImageFileController::class . ':actionCreate',
                    'name' => 'imageUpload'
                ],
                '/{id:\d+}' => [
                    'methods' => ['get'],
                    'action' => ImageController::class . ':actionIndex',
                    'name' => 'image'
                ]
            ],
            '/unit' => [
                '[/]' => [
                    'methods' => ['get'],
                    'action' => UnitController::class . ':actionIndex',
                    'name' => 'unit'
                ],
                '/update/{id:\d+}' => [
                    'methods' => ['post'],
                    'action' => UnitController::class . ':actionUpdate',
                    'name' => 'unitUpdate'
                ]
            ],
            '/oauth' => [
                '[/]' => [
                    'methods' => ['get'],
                    'action' => OauthController::class . ':actionIndex',
                    'name' => 'oauth'
                ],
                '/login' => [
                    'methods' => ['post'],
                    'action' => OauthController::class . ':actionLogin',
                    'name' => 'login'
                ],
                '/logout' => [
                    'methods' => ['post'],
                    'action' => OauthController::class . ':actionLogout',
                    'name' => 'logout'
                ],
            ],
        ],
    ],
];
