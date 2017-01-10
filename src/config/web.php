<?php

use controller\ImageController;
use controller\ImageFileController;
use controller\OauthController;
use controller\UnitController;

return [
    'siteUrl' => 'https://aigisu.ovh/',
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
                ],
                '/create' => [
                    'methods' => ['post'],
                    'action' => UnitController::class . ':actionCreate',
                    'name' => 'unitCreate'
                ],
                '/delete/{id:\d+}' => [
                    'methods' => ['get'],
                    'action' => UnitController::class . ':actionDelete',
                    'name' => 'unitDelete'
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
