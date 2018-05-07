<?php
return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'user',
    ],

    'guards' => [
        'api' => [
            'driver' => 'passport',
            'provider' => 'user',
        ],
    ],

    'providers' => [
        'user' => [
            'driver' => 'eloquent',
            'model' => \App\Model\User::class
        ]
    ]
];