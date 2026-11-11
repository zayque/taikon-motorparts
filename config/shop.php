<?php

declare(strict_types=1);

/*
 * Public business details shown in the header, footer, contact and about pages.
 * TODO before launch: replace the phone/WhatsApp placeholders with real numbers.
 */
return [
    'tagline'   => 'Parts, gear & workshop services for every rider.',
    'since'     => 2005,
    'phone'     => '+60 12-345 6789',
    'whatsapp'  => '60123456789',
    'email'     => 'taikonmotorparts@gmail.com',
    'address'   => [
        'line1'    => 'Jalan Kuang, Kampung Batu 20',
        'postcode' => '48050',
        'city'     => 'Rawang',
        'state'    => 'Selangor',
        'country'  => 'Malaysia',
    ],
    'map_query' => 'Kampung Batu 20, Kuang, 48050 Rawang, Selangor',
    'hours'     => [
        ['days' => 'Monday – Saturday', 'time' => '8:00 AM – 9:00 PM'],
        ['days' => 'Sunday',            'time' => '9:00 AM – 1:00 PM'],
    ],
];
