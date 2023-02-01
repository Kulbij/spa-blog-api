<?php

return [
    'frontend' => [
        'url' => env('APP_FRONT_URL'),
    ],
    'roles' => [
        'user' => (int)env('ROLES_USER', 1),
        'manager' => (int)env('ROLES_MANAGER', 2),
    ],

    'user' => [
        'password' => [
            'length' => (int)env('USER_PASSWORD_LENGTH', 8),
            'set' => [
                'link' => env('APP_SET_PASSWORD_URL'),
                'token' => [
                    'expired' => env('USER_PASSWORD_SET_TOKEN_EXPIRED', 24),
                ],
            ],
            'restore' => [
                'link' => env('APP_RESTORE_PASSWORD_URL'),
                'token' => [
                    'expired' => env('USER_PASSWORD_RESTORE_TOKEN_EXPIRED', 1),
                ],
            ],
        ],
    ],

    'trust' => [
        'hosts' => array_filter(explode(',', env('TRUST_HOSTS'))),
    ],

    'orders' => [
        'completed' => [
            'periods' => array_filter(explode(',', env('ORDER_COMPLETED_PERIODS'))),
            'export' => [
                'fileName' => env('ORDER_COMPLETED_EXPORT_FILENAME', 'orders'),
            ],
        ],
    ],

    'dishes' => [
        'popular' => [
            'count' => env('DISHES_POPULAR_COUNT', 1),
            'limit' => env('DISHES_POPULAR_LIMIT', 10),
        ],
        'recommended' => [
            'limit' => env('DISHES_RECOMMENDED_LIMIT', 10),
        ],
    ],

    'cache' => [
        'tags' => [
            'completed_orders' => env('CACHE_TAGS_ORDERS_COMPLETED', 86400),
            'translate' => env('CACHE_TAGS_TRANSLATE', 604800),
            'seo' => env('CACHE_TAGS_SEO', 604800),
        ],
    ],

    'routes' => [
        'rate_limiting' => [
            //per minute
            'maxAttempts' => env('ROUTES_RATE_LIMITING_MAX_ATTEMPTS', 60),
        ],
    ],
];
