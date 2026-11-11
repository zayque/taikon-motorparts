<?php

declare(strict_types=1);

return [
    'flat_rate' => 10.00,
    'free_over' => 300.00,

    'methods' => [
        'delivery' => 'Home delivery',
        'pickup'   => 'Self-pickup at workshop',
    ],

    'payment_methods' => [
        'cod'           => 'Cash on delivery',
        'bank_transfer' => 'Online bank transfer',
        'pay_at_store'  => 'Pay at workshop (pickup only)',
    ],

    // Shown to customers who choose bank transfer. TODO: set real account details.
    'bank' => [
        'name'    => 'Maybank',
        'account' => '5621 0000 0000',
        'holder'  => 'Taikon Motorparts',
    ],
];
