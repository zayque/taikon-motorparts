<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Support\Paginator;

final class User
{
    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM users WHERE id = ?', [$id]);
    }

    /** @return array<string, mixed>|null */
    public static function findByEmail(string $email): ?array
    {
        return Database::first('SELECT * FROM users WHERE email = ?', [strtolower(trim($email))]);
    }

    public static function emailTaken(string $email, ?int $exceptId = null): bool
    {
        return (bool) Database::value(
            'SELECT 1 FROM users WHERE email = ? AND id <> ?',
            [strtolower(trim($email)), $exceptId ?? 0]
        );
    }

    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return Database::insert('users', [
            'name'          => $data['name'],
            'email'         => strtolower(trim($data['email'])),
            'phone'         => $data['phone'] ?: null,
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role'          => $data['role'] ?? 'customer',
        ]);
    }

    /** @param array<string, mixed> $data */
    public static function updateProfile(int $id, array $data): void
    {
        Database::update('users', $id, [
            'name'         => $data['name'],
            'email'        => strtolower(trim($data['email'])),
            'phone'        => $data['phone'] ?: null,
            'address_line' => $data['address_line'] ?: null,
            'city'         => $data['city'] ?: null,
            'postcode'     => $data['postcode'] ?: null,
            'state'        => $data['state'] ?: null,
        ]);
    }

    public static function updatePassword(int $id, string $password): void
    {
        Database::update('users', $id, ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
    }

    public static function paginateCustomers(string $search, int $perPage): Paginator
    {
        $where = "WHERE u.role = 'customer'";
        $params = [];

        if ($search !== '') {
            $where .= ' AND (u.name LIKE :q1 OR u.email LIKE :q2 OR u.phone LIKE :q3)';
            $like = '%' . $search . '%';
            $params = ['q1' => $like, 'q2' => $like, 'q3' => $like];
        }

        $total = (int) Database::value("SELECT COUNT(*) FROM users u $where", $params);
        $page = new Paginator([], $total, $perPage);

        $page->items = Database::all(
            "SELECT u.*, COUNT(o.id) AS order_count, COALESCE(SUM(CASE WHEN o.status <> 'cancelled' THEN o.total END), 0) AS lifetime_value
             FROM users u LEFT JOIN orders o ON o.user_id = u.id
             $where GROUP BY u.id ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset",
            $params + ['limit' => $perPage, 'offset' => $page->offset]
        );

        return $page;
    }

    public static function countCustomers(): int
    {
        return (int) Database::value("SELECT COUNT(*) FROM users WHERE role = 'customer'");
    }
}
