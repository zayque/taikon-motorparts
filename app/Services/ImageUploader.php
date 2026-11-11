<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

/**
 * Validates and stores uploaded product images under public/uploads/products.
 * The real MIME type is sniffed from file contents (not trusted from the client)
 * and files get random names so they can never overwrite each other or run as code.
 */
final class ImageUploader
{
    /** @param array<string, mixed> $file an entry from $_FILES */
    public static function store(array $file): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException(match ($file['error'] ?? null) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Image is too large.',
                UPLOAD_ERR_PARTIAL => 'Upload was interrupted. Please try again.',
                default => 'Image upload failed.',
            });
        }

        if ((int) $file['size'] > (int) config('uploads.max_bytes')) {
            throw new RuntimeException('Image must be 2 MB or smaller.');
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Invalid upload.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $extension = config('uploads.mime_types')[$mime] ?? null;
        if ($extension === null || @getimagesize($file['tmp_name']) === false) {
            throw new RuntimeException('Image must be a JPG, PNG or WebP file.');
        }

        $name = bin2hex(random_bytes(12)) . '.' . $extension;
        $target = self::directory() . '/' . $name;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new RuntimeException('Could not save the image.');
        }

        return $name;
    }

    public static function delete(?string $filename): void
    {
        if ($filename === null || $filename === '' || basename($filename) !== $filename) {
            return;
        }

        $path = self::directory() . '/' . $filename;
        if (is_file($path)) {
            unlink($path);
        }
    }

    private static function directory(): string
    {
        $dir = BASE_PATH . '/public/' . config('uploads.products_dir');
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException('Upload directory is not writable.');
        }

        return $dir;
    }
}
