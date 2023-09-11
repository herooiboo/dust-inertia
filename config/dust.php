<?php

use Dust\Http\Router\Enum\RoutePath;
use Dust\Http\Router\Enum\Router;

return [
    'modules' => [
        'defaults' => [
            'path' => 'Modules',
        ],
        'paths' => [
            'Modules',
        ],
    ],
    'guards' => [
        'api' => [
            'routes' => [
                'type' => Router::Attribute,
                'path' => RoutePath::None, // should be none for route type Router::Attribute
                'file_name' => null, // should be null for route type Router::Attribute
            ],
            'prefix' => 'api',
            'middleware' => 'api',
            'rate_limit_max' => 60,
        ],
        'playground' => [
            'routes' => [
                'type' => Router::File,
                'path' => RoutePath::Root, // should not be none for route type Router::File
                'file_name' => 'playground', // should not be null for route type Router::File
            ],
            'prefix' => 'playground',
            'middleware' => 'playground',
            'rate_limit_max' => 0,
        ],
    ],
    'logging' => [
        'channels' => [
            'info'      => 'daily',
            'debug'     => 'daily',
            'warning'   => 'daily',
            'error'     => 'daily',
            'emergency' => 'daily',
            'critical'  => 'daily',
        ]
    ]
];
