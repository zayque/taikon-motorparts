<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Business rules for which dates and times can be booked.
 */
final class BookingCalendar
{
    public static function minDate(): string
    {
        return date('Y-m-d', strtotime('+1 day'));
    }

    public static function maxDate(): string
    {
        return date('Y-m-d', strtotime('+' . (int) config('booking.max_days_ahead') . ' days'));
    }

    public static function isBookable(string $date): bool
    {
        $d = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        return $d !== false
            && $d->format('Y-m-d') === $date
            && $date >= self::minDate()
            && $date <= self::maxDate();
    }

    /** @return list<string> */
    public static function slotsFor(string $date): array
    {
        $isSunday = date('w', strtotime($date)) === '0';

        return $isSunday ? config('booking.sunday_slots') : config('booking.slots');
    }
}
