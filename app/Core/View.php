<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Plain-PHP templates. A view renders into `$content`, which is then
 * injected into the chosen layout. Views can set `$title`, `$description`
 * and other layout variables via View::share().
 */
final class View
{
    /** @var array<string, mixed> */
    private static array $shared = [];

    public static function share(string $key, mixed $value): void
    {
        self::$shared[$key] = $value;
    }

    /** @param array<string, mixed> $data */
    public static function render(string $view, array $data = [], ?string $layout = 'main'): string
    {
        $content = self::partial($view, $data);

        if ($layout === null) {
            return $content;
        }

        return self::partial('layouts/' . $layout, array_merge($data, ['content' => $content]));
    }

    /** @param array<string, mixed> $data */
    public static function partial(string $view, array $data = []): string
    {
        $file = BASE_PATH . '/views/' . $view . '.php';
        if (!is_file($file)) {
            throw new RuntimeException("View [$view] not found.");
        }

        $data = array_merge(self::$shared, $data);

        ob_start();
        (static function (string $__file, array $__data): void {
            extract($__data, EXTR_SKIP);
            require $__file;
        })($file, $data);

        return (string) ob_get_clean();
    }
}
