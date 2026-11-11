<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Support\Paginator;

final class Product
{
    private const SELECT = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug, c.icon AS category_icon
                            FROM products p JOIN categories c ON c.id = p.category_id';

    public const SORTS = [
        'newest'     => 'p.created_at DESC, p.id DESC',
        'price_asc'  => 'p.price ASC',
        'price_desc' => 'p.price DESC',
        'name'       => 'p.name ASC',
    ];

    /**
     * Storefront listing with optional category, search and sort.
     *
     * @param array{category_id?: int|null, q?: string, sort?: string, in_stock?: bool} $filters
     */
    public static function paginateForShop(array $filters, int $perPage): Paginator
    {
        $where = ['p.is_active = 1'];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = :category_id';
            $params['category_id'] = $filters['category_id'];
        }

        if (($filters['q'] ?? '') !== '') {
            $where[] = '(p.name LIKE :q1 OR p.brand LIKE :q2 OR p.fits LIKE :q3 OR p.sku LIKE :q4)';
            $like = '%' . $filters['q'] . '%';
            $params += ['q1' => $like, 'q2' => $like, 'q3' => $like, 'q4' => $like];
        }

        if (!empty($filters['in_stock'])) {
            $where[] = 'p.stock > 0';
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);
        $order = self::SORTS[$filters['sort'] ?? 'newest'] ?? self::SORTS['newest'];

        $total = (int) Database::value("SELECT COUNT(*) FROM products p $whereSql", $params);
        $page = new Paginator([], $total, $perPage);
        $page->items = Database::all(
            self::SELECT . " $whereSql ORDER BY $order LIMIT :limit OFFSET :offset",
            $params + ['limit' => $perPage, 'offset' => $page->offset]
        );

        return $page;
    }

    /** @return list<array<string, mixed>> */
    public static function featured(int $limit = 8): array
    {
        return Database::all(
            self::SELECT . ' WHERE p.is_active = 1 AND p.is_featured = 1 ORDER BY p.updated_at DESC LIMIT :limit',
            ['limit' => $limit]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function related(array $product, int $limit = 4): array
    {
        return Database::all(
            self::SELECT . ' WHERE p.is_active = 1 AND p.category_id = :cat AND p.id <> :id ORDER BY p.is_featured DESC, RAND() LIMIT :limit',
            ['cat' => (int) $product['category_id'], 'id' => (int) $product['id'], 'limit' => $limit]
        );
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::first(self::SELECT . ' WHERE p.id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findActiveBySlug(string $slug): ?array
    {
        return Database::first(self::SELECT . ' WHERE p.slug = ? AND p.is_active = 1', [$slug]);
    }

    /**
     * @param list<int> $ids
     * @return array<int, array<string, mixed>> keyed by id
     */
    public static function findMany(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $rows = Database::all(self::SELECT . " WHERE p.id IN ($placeholders) AND p.is_active = 1", array_values($ids));

        return array_column($rows, null, 'id');
    }

    // ---- Admin -------------------------------------------------------------

    /** @param array{q?: string, category_id?: int|null, stock?: string} $filters */
    public static function paginateForAdmin(array $filters, int $perPage): Paginator
    {
        $where = [];
        $params = [];

        if (($filters['q'] ?? '') !== '') {
            $where[] = '(p.name LIKE :q1 OR p.sku LIKE :q2 OR p.brand LIKE :q3)';
            $like = '%' . $filters['q'] . '%';
            $params += ['q1' => $like, 'q2' => $like, 'q3' => $like];
        }
        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = :category_id';
            $params['category_id'] = $filters['category_id'];
        }
        if (($filters['stock'] ?? '') === 'low') {
            $where[] = 'p.stock <= :low';
            $params['low'] = (int) config('catalog.low_stock_threshold');
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total = (int) Database::value("SELECT COUNT(*) FROM products p $whereSql", $params);
        $page = new Paginator([], $total, $perPage);
        $page->items = Database::all(
            self::SELECT . " $whereSql ORDER BY p.updated_at DESC, p.id DESC LIMIT :limit OFFSET :offset",
            $params + ['limit' => $perPage, 'offset' => $page->offset]
        );

        return $page;
    }

    /** @return list<array<string, mixed>> */
    public static function lowStock(int $limit = 6): array
    {
        return Database::all(
            'SELECT id, name, sku, stock FROM products WHERE is_active = 1 AND stock <= :low ORDER BY stock ASC LIMIT :limit',
            ['low' => (int) config('catalog.low_stock_threshold'), 'limit' => $limit]
        );
    }

    public static function slugTaken(string $slug, ?int $exceptId = null): bool
    {
        return (bool) Database::value('SELECT 1 FROM products WHERE slug = ? AND id <> ?', [$slug, $exceptId ?? 0]);
    }

    public static function skuTaken(string $sku, ?int $exceptId = null): bool
    {
        return (bool) Database::value('SELECT 1 FROM products WHERE sku = ? AND id <> ?', [$sku, $exceptId ?? 0]);
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return Database::insert('products', $data);
    }

    /** @param array<string, mixed> $data */
    public static function update(int $id, array $data): void
    {
        Database::update('products', $id, $data);
    }

    public static function delete(int $id): void
    {
        Database::delete('products', $id);
    }

    public static function count(): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM products WHERE is_active = 1');
    }
}
