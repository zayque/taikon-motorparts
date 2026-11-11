<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Category
{
    /** @return list<array<string, mixed>> categories with active product counts */
    public static function all(): array
    {
        return Database::all(
            'SELECT c.*, COUNT(p.id) AS product_count
             FROM categories c LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
             GROUP BY c.id ORDER BY c.sort_order, c.name'
        );
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM categories WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findBySlug(string $slug): ?array
    {
        return Database::first('SELECT * FROM categories WHERE slug = ?', [$slug]);
    }

    public static function slugTaken(string $slug, ?int $exceptId = null): bool
    {
        return (bool) Database::value('SELECT 1 FROM categories WHERE slug = ? AND id <> ?', [$slug, $exceptId ?? 0]);
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return Database::insert('categories', self::columns($data));
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        Database::update('categories', $id, self::columns($data));
    }

    public static function hasProducts(int $id): bool
    {
        return (bool) Database::value('SELECT 1 FROM products WHERE category_id = ? LIMIT 1', [$id]);
    }

    public static function delete(int $id): void
    {
        Database::delete('categories', $id);
    }

    /** @param array<string, mixed> $data @return array<string, mixed> */
    private static function columns(array $data): array
    {
        return [
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'description' => $data['description'] ?: null,
            'icon'        => $data['icon'] ?: 'box',
            'sort_order'  => (int) ($data['sort_order'] ?: 0),
        ];
    }
}
