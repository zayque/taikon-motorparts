<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Support\Paginator;

final class Message
{
    /** @param array<string, mixed> $data */
    public static function create(array $data): int
    {
        return Database::insert('messages', [
            'name'    => $data['name'],
            'email'   => strtolower($data['email']),
            'phone'   => $data['phone'] ?: null,
            'subject' => $data['subject'],
            'body'    => $data['body'],
        ]);
    }

    /** @return array<string, mixed>|null */
    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM messages WHERE id = ?', [$id]);
    }

    public static function paginate(string $filter, int $perPage): Paginator
    {
        $where = $filter === 'unread' ? 'WHERE is_read = 0' : '';
        $total = (int) Database::value("SELECT COUNT(*) FROM messages $where");
        $page = new Paginator([], $total, $perPage);
        $page->items = Database::all(
            "SELECT * FROM messages $where ORDER BY created_at DESC, id DESC LIMIT :limit OFFSET :offset",
            ['limit' => $perPage, 'offset' => $page->offset]
        );

        return $page;
    }

    public static function countUnread(): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM messages WHERE is_read = 0');
    }

    public static function markRead(int $id): void
    {
        Database::update('messages', $id, ['is_read' => 1]);
    }

    public static function delete(int $id): void
    {
        Database::delete('messages', $id);
    }
}
