<?php

use App\Model\User;

return [
    'defaults' => [
        'guard'     => 'api',
        'passwords' => 'user',
    ],

    'guards' => [
        'api' => [
            'driver'   => 'passport',
            'provider' => 'user',
        ],
    ],

    'providers' => [
        'user' => [
            'driver' => 'eloquent',
            'model'  => User::class,
        ],
    ],
];
