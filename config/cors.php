<?php

return [
    'paths' => ['*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://greensoouq-dev.web.app',
        'https://green-soouq.web.app',
        'https://green-soouq.firebaseapp.com',
        'http://localhost:*',
    ],
    'allowed_origins_patterns' => [
        '#^https://greensoouq-dash\.(web\.app|vercel\.app)$#',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
