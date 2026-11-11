<?php

declare(strict_types=1);

namespace App\Support;

use App\Core\Request;
use App\Core\Url;

/**
 * Value object describing one page of results.
 */
final class Paginator
{
    public readonly int $page;
    public readonly int $lastPage;
    public readonly int $offset;

    /** @param list<array<string, mixed>> $items */
    public function __construct(
        public array $items,
        public readonly int $total,
        public readonly int $perPage,
        ?int $page = null,
    ) {
        $this->lastPage = max(1, (int) ceil($total / max(1, $perPage)));
        $this->page = min(max(1, $page ?? self::currentPage()), $this->lastPage);
        $this->offset = ($this->page - 1) * $perPage;
    }

    public static function currentPage(): int
    {
        return max(1, (int) Request::query('page', 1));
    }

    public static function offsetFor(int $perPage): int
    {
        return (self::currentPage() - 1) * $perPage;
    }

    public function hasPages(): bool
    {
        return $this->lastPage > 1;
    }

    public function from(): int
    {
        return $this->total === 0 ? 0 : $this->offset + 1;
    }

    public function to(): int
    {
        return min($this->offset + $this->perPage, $this->total);
    }

    /** URL for a page, preserving the current query string. */
    public function url(int $page): string
    {
        $query = $_GET;
        $query['page'] = $page > 1 ? $page : null;

        return Url::to(Request::path(), $query);
    }

    /** @return list<int|null> page numbers with null as an ellipsis gap */
    public function window(): array
    {
        $pages = [];
        for ($i = 1; $i <= $this->lastPage; $i++) {
            if ($i === 1 || $i === $this->lastPage || abs($i - $this->page) <= 1) {
                $pages[] = $i;
            } elseif (end($pages) !== null) {
                $pages[] = null;
            }
        }

        return $pages;
    }
}
