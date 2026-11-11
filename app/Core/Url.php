<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Builds URLs that work whether the app is served from the domain root
 * (`php -S`, a vhost) or from a sub-folder such as http://localhost/taikon_motorparts.
 */
final class Url
{
    private static ?string $basePath = null;

    public static function basePath(): string
    {
        if (self::$basePath !== null) {
            return self::$basePath;
        }

        $configured = (string) Config::get('app.url', '');
        if ($configured !== '') {
            return self::$basePath = rtrim((string) parse_url($configured, PHP_URL_PATH), '/');
        }

        // e.g. /taikon_motorparts/public/index.php -> /taikon_motorparts
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        $dir = preg_replace('#/public$#', '', $dir) ?? '';

        return self::$basePath = rtrim($dir, '/');
    }

    /** @param array<string, scalar|null> $query */
    public static function to(string $path = '/', array $query = []): string
    {
        $url = self::basePath() . '/' . ltrim($path, '/');
        $query = array_filter($query, static fn ($v) => $v !== null && $v !== '');

        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return $url === '' ? '/' : $url;
    }

    public static function asset(string $path): string
    {
        $file = BASE_PATH . '/public/' . ltrim($path, '/');
        $version = is_file($file) ? '?v=' . filemtime($file) : '';

        return self::to($path) . $version;
    }
}
