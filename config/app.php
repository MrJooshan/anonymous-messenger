<?php
return [
    'env'   => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL),
    'url'   => $_ENV['APP_URL'] ?? 'http://localhost:8000',
    'key'   => $_ENV['APP_KEY'] ?? '',
    'name'  => 'پیام ناشناس',
];