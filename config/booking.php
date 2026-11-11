<?php

declare(strict_types=1);

/*
 * Workshop booking rules. Slots are start times (24h); capacity is how many
 * bikes the workshop can take in the same slot.
 */
return [
    'slots'          => ['09:00', '10:30', '12:00', '14:30', '16:00', '17:30'],
    'sunday_slots'   => ['09:00', '10:30'],
    'slot_capacity'  => 3,
    'max_days_ahead' => 45,
];
