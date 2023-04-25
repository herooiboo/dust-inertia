<?php

return [
    'modules' => [
        'defaults' => [
            'path' => 'Modules',
        ],
        'paths'    => [
            'Modules',
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
