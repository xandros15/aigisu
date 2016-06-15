<?php

use controller\ImageController;
use controller\ImageFileController;
use controller\UnitController;
use controller\OauthController;

return [
    'siteUrl' => 'http://aigisu.pl',
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
            [
                'pattern' => '/',
                'methods' => ['get'],
                'action' => UnitController::class . ':actionIndex',
                'name' => 'home'
            ],
            [
                'pattern' => '/image',
                'groups' => [
                    [
                        'pattern' => '/upload/{id:\d+}',
                        'methods' => ['post'],
                        'action' => ImageFileController::class . ':actionCreate',
                        'name' => 'imageUpload'
                    ],
                    [
                        'pattern' => '/{id:\d+}',
                        'methods' => ['get'],
                        'action' => ImageController::class . ':actionIndex',
                        'name' => 'image'
                    ]
                ]
            ],
            [
                'pattern' => '/unit',
                'groups' => [
                    [
                        'pattern' => '[/]',
                        'methods' => ['get'],
                        'action' => UnitController::class . ':actionIndex',
                        'name' => 'unit'
                    ],
                    [
                        'pattern' => '/update/{id:\d+}',
                        'methods' => ['post'],
                        'action' => UnitController::class . ':actionUpdate',
                        'name' => 'unitUpdate'
                    ],
                    [
                        'pattern' => '/create',
                        'methods' => ['post'],
                        'action' => UnitController::class . ':actionCreate',
                        'name' => 'unitCreate'
                    ],
                    [
                        'pattern' => '/delete/{id:\d+}',
                        'methods' => ['get'],
                        'action' => UnitController::class . ':actionDelete',
                        'name' => 'unitDelete'
                    ]
                ]
            ],
            [
                'pattern' => '/oauth',
                'groups' => [
                    [
                        'pattern' => '[/]',
                        'methods' => ['get'],
                        'action' => OauthController::class . ':actionIndex',
                        'name' => 'oauth'
                    ],
                    [
                        'pattern' => '/login',
                        'methods' => ['post'],
                        'action' => OauthController::class . ':actionLogin',
                        'name' => 'login'
                    ],
                    [
                        'pattern' => '/logout',
                        'methods' => ['post'],
                        'action' => OauthController::class . ':actionLogout',
                        'name' => 'logout'
                    ]
                ]
            ],
        ],
    ],
];
