<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'ldap',
            'remember' => true, // Garante que o remember está habilitado
        ],
    ],

    'providers' => [
        'ldap' => [
            'driver' => 'ldap',
            'model' => LdapRecord\Models\ActiveDirectory\User::class,
            'rules' => [],
            'database' => [
                'model' => App\Models\User::class,
                'sync_passwords' => false,
                'sync_attributes' => [
                    'username' => 'samaccountname',
                    'name' => 'cn',
                    'email' => 'mail',
                ],
                'sync_existing' => true,
                'guid_key' => 'objectguid',
            ],
        ],
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];