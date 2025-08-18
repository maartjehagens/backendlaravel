<?php declare(strict_types=1);

return [
    'default' => env('ELASTIC_CLIENT_CONNECTION', 'default'),
    'connections' => [
        'default' => [
            'hosts' => [
                env('ELASTIC_HOST', '127.0.0.1:9200'),
            ],
        ],
    ],
];
