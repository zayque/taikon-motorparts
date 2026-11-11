<?php

declare(strict_types=1);

return [
    // Relative to /public
    'products_dir' => 'uploads/products',
    'max_bytes'    => 2 * 1024 * 1024,
    'mime_types'   => [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ],
];
