<?php

return [
    'env'       => env('ELASTIC_ENV', 'local'),

    // Local/self-managed
    'hosts'     => explode(',', env('ELASTIC_HOSTS', 'http://127.0.0.1:9200')),
    'username'  => env('ELASTIC_USERNAME'),
    'password'  => env('ELASTIC_PASSWORD'),

    // Elastic Cloud
    'cloud_id'  => env('ELASTIC_CLOUD_ID'),
    'api_key'   => env('ELASTIC_API_KEY'),
];
