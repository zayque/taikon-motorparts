<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Service
{
    /** @return list<array<string, mixed>> */
    public static function active(): array
    {
        return array_map(
            [self::class, 'hydrate'],
            Database::all('SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order, name')
        );
    }

    /** @return list<array<string, mixed>> */
    public static function all(): array
    {
        return array_map(
            [self::class, 'hydrate'],
            Database::all(
                'SELECT s.*, (SELECT COUNT(*) FROM bookings b WHERE b.service_id = s.id) AS booking_count
                 FROM services s ORDER BY s.sort_order, s.name'
            )
        );
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        $row = Database::first('SELECT * FROM services WHERE id = ?', [$id]);

        return $row ? self::hydrate($row) : null;
    }

    /** @return array<string, mixed>|null */
    public static function findActive(int $id): ?array
    {
        $row = Database::first('SELECT * FROM services WHERE id = ? AND is_active = 1', [$id]);

        return $row ? self::hydrate($row) : null;
    }

    public static function slugTaken(string $slug, ?int $exceptId = null): bool
    {
        return (bool) Database::value('SELECT 1 FROM services WHERE slug = ? AND id <> ?', [$slug, $exceptId ?? 0]);
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return Database::insert('services', $data);
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        Database::update('services', $id, $data);
    }

    public static function delete(int $id): void
    {
        Database::delete('services', $id);
    }

    /** @param array<string, mixed> $row @return array<string, mixed> */
    private static function hydrate(array $row): array
    {
        $row['feature_list'] = array_values(array_filter(array_map('trim', explode("\n", (string) $row['features']))));

        return $row;
    }
}
