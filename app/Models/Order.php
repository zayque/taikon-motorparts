<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Support\Paginator;

final class Order
{
    public const STATUSES = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM orders WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findForUser(string $orderNumber, int $userId): ?array
    {
        return Database::first('SELECT * FROM orders WHERE order_number = ? AND user_id = ?', [$orderNumber, $userId]);
    }

    /** @return list<array<string, mixed>> */
    public static function items(int $orderId): array
    {
        return Database::all(
            'SELECT oi.*, p.slug, p.image, p.is_active
             FROM order_items oi LEFT JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ? ORDER BY oi.id',
            [$orderId]
        );
    }

    /** @return list<array<string, mixed>> */
    public static function forUser(int $userId): array
    {
        return Database::all(
            'SELECT o.*, (SELECT SUM(quantity) FROM order_items WHERE order_id = o.id) AS item_count
             FROM orders o WHERE o.user_id = ? ORDER BY o.created_at DESC',
            [$userId]
        );
    }

    /** @param array{q?: string, status?: string} $filters */
    public static function paginateForAdmin(array $filters, int $perPage): Paginator
    {
        $where = [];
        $params = [];

        if (in_array($filters['status'] ?? '', self::STATUSES, true)) {
            $where[] = 'status = :status';
            $params['status'] = $filters['status'];
        }
        if (($filters['q'] ?? '') !== '') {
            $where[] = '(order_number LIKE :q1 OR customer_name LIKE :q2 OR email LIKE :q3 OR phone LIKE :q4)';
            $like = '%' . $filters['q'] . '%';
            $params += ['q1' => $like, 'q2' => $like, 'q3' => $like, 'q4' => $like];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total = (int) Database::value("SELECT COUNT(*) FROM orders $whereSql", $params);
        $page = new Paginator([], $total, $perPage);
        $page->items = Database::all(
            "SELECT * FROM orders $whereSql ORDER BY created_at DESC, id DESC LIMIT :limit OFFSET :offset",
            $params + ['limit' => $perPage, 'offset' => $page->offset]
        );

        return $page;
    }

    /** @return list<array<string, mixed>> */
    public static function recent(int $limit = 6): array
    {
        return Database::all('SELECT * FROM orders ORDER BY created_at DESC, id DESC LIMIT :limit', ['limit' => $limit]);
    }

    /** @return array<string, int> */
    public static function countsByStatus(): array
    {
        $rows = Database::all('SELECT status, COUNT(*) AS n FROM orders GROUP BY status');
        $counts = array_fill_keys(self::STATUSES, 0);
        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['n'];
        }

        return $counts;
    }

    public static function revenueSince(string $date): float
    {
        return (float) Database::value(
            "SELECT COALESCE(SUM(total), 0) FROM orders WHERE status <> 'cancelled' AND created_at >= ?",
            [$date]
        );
    }

    /** @return list<array{day: string, total: float}> revenue per day for the last N days (zero-filled) */
    public static function dailyRevenue(int $days = 14): array
    {
        $rows = Database::all(
            "SELECT DATE(created_at) AS day, SUM(total) AS total FROM orders
             WHERE status <> 'cancelled' AND created_at >= (CURDATE() - INTERVAL :days DAY)
             GROUP BY DATE(created_at)",
            ['days' => $days - 1]
        );
        $map = array_column($rows, 'total', 'day');

        $series = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-$i days"));
            $series[] = ['day' => $day, 'total' => (float) ($map[$day] ?? 0)];
        }

        return $series;
    }

    /**
     * Change status. Cancelling returns stock; un-cancelling takes it again.
     */
    public static function updateStatus(int $id, string $status): void
    {
        Database::transaction(static function () use ($id, $status): void {
            $order = Database::first('SELECT status FROM orders WHERE id = ? FOR UPDATE', [$id]);
            if ($order === null || $order['status'] === $status) {
                return;
            }

            $wasCancelled = $order['status'] === 'cancelled';
            $nowCancelled = $status === 'cancelled';

            if ($wasCancelled !== $nowCancelled) {
                $sign = $nowCancelled ? '+' : '-';
                Database::query(
                    "UPDATE products p JOIN order_items oi ON oi.product_id = p.id
                     SET p.stock = GREATEST(0, p.stock $sign oi.quantity)
                     WHERE oi.order_id = ?",
                    [$id]
                );
            }

            Database::update('orders', $id, ['status' => $status]);
        });
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'TM' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
        } while (Database::value('SELECT 1 FROM orders WHERE order_number = ?', [$number]));

        return $number;
    }
}
