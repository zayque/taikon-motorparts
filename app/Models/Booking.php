<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Support\Paginator;

final class Booking
{
    public const STATUSES = ['pending', 'confirmed', 'completed', 'cancelled'];

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM bookings WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findByNumber(string $number): ?array
    {
        return Database::first('SELECT * FROM bookings WHERE booking_number = ?', [$number]);
    }

    /** @return list<array<string, mixed>> */
    public static function forUser(int $userId): array
    {
        return Database::all(
            'SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC, booking_time DESC',
            [$userId]
        );
    }

    /**
     * Remaining capacity per time slot on a given date.
     *
     * @param list<string> $slots e.g. ['09:00', '10:30']
     * @return array<string, int>
     */
    public static function availability(string $date, array $slots, int $capacity): array
    {
        $rows = Database::all(
            "SELECT TIME_FORMAT(booking_time, '%H:%i') AS slot, COUNT(*) AS n
             FROM bookings WHERE booking_date = ? AND status <> 'cancelled' GROUP BY booking_time",
            [$date]
        );
        $taken = array_column($rows, 'n', 'slot');

        $out = [];
        foreach ($slots as $slot) {
            $out[$slot] = max(0, $capacity - (int) ($taken[$slot] ?? 0));
        }

        return $out;
    }

    /**
     * Insert a booking if the slot still has capacity. Returns the new id or null if full.
     *
     * @param array<string, mixed> $data
     */
    public static function createIfAvailable(array $data, int $capacity): ?int
    {
        return Database::transaction(static function () use ($data, $capacity): ?int {
            $taken = (int) Database::value(
                "SELECT COUNT(*) FROM bookings WHERE booking_date = ? AND booking_time = ? AND status <> 'cancelled' FOR UPDATE",
                [$data['booking_date'], $data['booking_time']]
            );

            if ($taken >= $capacity) {
                return null;
            }

            $data['booking_number'] = self::generateNumber();

            return Database::insert('bookings', $data);
        });
    }

    /** @param array{q?: string, status?: string, when?: string} $filters */
    public static function paginateForAdmin(array $filters, int $perPage): Paginator
    {
        $where = [];
        $params = [];

        if (in_array($filters['status'] ?? '', self::STATUSES, true)) {
            $where[] = 'status = :status';
            $params['status'] = $filters['status'];
        }
        if (($filters['when'] ?? '') === 'upcoming') {
            $where[] = 'booking_date >= CURDATE()';
        } elseif (($filters['when'] ?? '') === 'past') {
            $where[] = 'booking_date < CURDATE()';
        }
        if (($filters['q'] ?? '') !== '') {
            $where[] = '(booking_number LIKE :q1 OR customer_name LIKE :q2 OR phone LIKE :q3 OR plate_number LIKE :q4)';
            $like = '%' . $filters['q'] . '%';
            $params += ['q1' => $like, 'q2' => $like, 'q3' => $like, 'q4' => $like];
        }

        $order = ($filters['when'] ?? '') === 'upcoming' ? 'booking_date ASC, booking_time ASC' : 'booking_date DESC, booking_time DESC';
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total = (int) Database::value("SELECT COUNT(*) FROM bookings $whereSql", $params);
        $page = new Paginator([], $total, $perPage);
        $page->items = Database::all(
            "SELECT * FROM bookings $whereSql ORDER BY $order LIMIT :limit OFFSET :offset",
            $params + ['limit' => $perPage, 'offset' => $page->offset]
        );

        return $page;
    }

    /** @return list<array<string, mixed>> */
    public static function upcoming(int $limit = 6): array
    {
        return Database::all(
            "SELECT * FROM bookings WHERE booking_date >= CURDATE() AND status IN ('pending','confirmed')
             ORDER BY booking_date, booking_time LIMIT :limit",
            ['limit' => $limit]
        );
    }

    public static function countPending(): int
    {
        return (int) Database::value("SELECT COUNT(*) FROM bookings WHERE status = 'pending'");
    }

    public static function updateStatus(int $id, string $status): void
    {
        Database::update('bookings', $id, ['status' => $status]);
    }

    public static function cancelForUser(int $id, int $userId): bool
    {
        return Database::query(
            "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending' AND booking_date > CURDATE()",
            [$id, $userId]
        )->rowCount() > 0;
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'BK' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
        } while (Database::value('SELECT 1 FROM bookings WHERE booking_number = ?', [$number]));

        return $number;
    }
}
