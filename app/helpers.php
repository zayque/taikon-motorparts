<?php

declare(strict_types=1);

/*
 * Global view and controller helpers. Kept deliberately small;
 * anything with real logic belongs in a class.
 */

use App\Core\Auth;
use App\Core\Config;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Session;
use App\Core\Url;
use App\Support\Icons;

/** Escape for HTML output. Use for every dynamic value in a view. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}

/** @param array<string, scalar|null> $query */
function url(string $path = '/', array $query = []): string
{
    return Url::to($path, $query);
}

function asset(string $path): string
{
    return Url::asset($path);
}

function upload_url(?string $filename): ?string
{
    return $filename ? Url::to(config('uploads.products_dir') . '/' . $filename) : null;
}

function money(float|int|string|null $amount): string
{
    return config('app.currency') . ' ' . number_format((float) $amount, 2);
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

function old(string $key, mixed $default = ''): mixed
{
    return Session::getFlash('_old', [])[$key] ?? $default;
}

/** @return array<string, string> */
function errors(): array
{
    return Session::getFlash('_errors', []);
}

function error(string $field): ?string
{
    return errors()[$field] ?? null;
}

/** Inline error message markup for a form field (empty string when valid). */
function field_error(string $field): string
{
    $message = error($field);

    return $message === null
        ? ''
        : '<p class="field-error" id="' . e($field) . '-error">' . icon('alert') . e($message) . '</p>';
}

/** Extra attributes for an input so screen readers announce its error. */
function field_attrs(string $field): string
{
    return error($field) !== null
        ? 'aria-invalid="true" aria-describedby="' . e($field) . '-error"'
        : '';
}

function field_class(string $field): string
{
    return error($field) !== null ? 'field has-error' : 'field';
}

function flash(string $type, string $message): void
{
    Session::flash('_message', ['type' => $type, 'text' => $message]);
}

/** Redirect to an app path such as "/cart". */
function redirect(string $path, array $query = []): never
{
    header('Location: ' . Url::to($path, $query), true, 302);
    exit;
}

/**
 * Redirect to a full same-site URI (e.g. a stored "intended" REQUEST_URI).
 * Anything that is not a local absolute path falls back to $fallback.
 */
function redirect_uri(?string $uri, string $fallback = '/'): never
{
    if (is_string($uri) && str_starts_with($uri, '/') && !str_starts_with($uri, '//') && !str_contains($uri, '\\')) {
        header('Location: ' . $uri, true, 302);
        exit;
    }

    redirect($fallback);
}

/** Redirect to the previous page (same-origin only) or a fallback path. */
function back(string $fallback = '/'): never
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $host = $_SERVER['HTTP_HOST'] ?? '';

    if ($referer !== '' && parse_url($referer, PHP_URL_HOST) === $host) {
        header('Location: ' . $referer, true, 302);
        exit;
    }

    redirect($fallback);
}

function auth_user(): ?array
{
    return Auth::user();
}

/** True when the current path starts with the given prefix (for nav highlighting). */
function is_active(string $prefix, bool $exact = false): bool
{
    $path = Request::path();

    return $exact ? $path === $prefix : ($path === $prefix || str_starts_with($path, rtrim($prefix, '/') . '/'));
}

/** Render a view partial, e.g. partial('partials/product-card', ['product' => $p]). */
function partial(string $view, array $data = []): string
{
    return \App\Core\View::partial($view, $data);
}

function icon(string $name, string $class = 'icon'): string
{
    return Icons::svg($name, $class);
}

function status_badge(string $status): string
{
    $tone = match ($status) {
        'pending'                 => 'warning',
        'processing', 'confirmed' => 'info',
        'shipped'                 => 'accent',
        'completed'               => 'success',
        'cancelled'               => 'danger',
        default                   => 'neutral',
    };

    return '<span class="badge badge--' . $tone . '">' . e(ucfirst($status)) . '</span>';
}

function format_date(?string $date, string $format = 'j M Y'): string
{
    return $date ? date($format, strtotime($date)) : '—';
}

function str_excerpt(?string $text, int $length = 110): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)) ?? '');

    return mb_strlen($text) > $length ? rtrim(mb_substr($text, 0, $length)) . '…' : $text;
}

function slugify(string $text): string
{
    $text = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $text) ?? '', '-'));

    return $text !== '' ? $text : bin2hex(random_bytes(4));
}
