<?php

declare(strict_types=1);

use App\Core\Env;

/*
 * Core application settings. Environment-specific values come from `.env`.
 * Business settings live in their own files: shop.php, shipping.php,
 * booking.php, catalog.php and uploads.php.
 */
return [
    'name'     => Env::get('APP_NAME', 'Taikon Motorparts'),
    'env'      => Env::get('APP_ENV', 'production'),
    'debug'    => Env::bool('APP_DEBUG', false),
    'url'      => rtrim((string) Env::get('APP_URL', ''), '/'),
    'timezone' => Env::get('APP_TIMEZONE', 'Asia/Kuala_Lumpur'),
    'currency' => 'RM',

    'session' => [
        'name'     => 'taikon_session',
        'secure'   => Env::bool('SESSION_SECURE', false),
        'lifetime' => 60 * 60 * 24 * 7,
    ],
];
