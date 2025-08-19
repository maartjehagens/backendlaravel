<?php 

return [
    'connection' => [
        // Laat host/port weg; we gebruiken Cloud ID + API key
        'cloud_id' => env('ELASTIC_CLOUD_ID'),
        'headers'  => [
            'Authorization' => 'ApiKey '.env('ELASTIC_API_KEY'),
        ],
        'scheme'   => 'https',
    ],
    'indexes' => [
        \App\Models\Product::class,
    ],
];