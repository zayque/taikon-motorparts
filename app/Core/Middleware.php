<?php

declare(strict_types=1);

namespace App\Core;

final class Middleware
{
    public static function handle(string $name): void
    {
        match ($name) {
            'auth'  => self::auth(),
            'guest' => self::guest(),
            'admin' => self::admin(),
            default => throw new \InvalidArgumentException("Unknown middleware [$name]"),
        };
    }

    private static function auth(): void
    {
        if (!Auth::check()) {
            Session::put('intended', $_SERVER['REQUEST_URI'] ?? Url::to('/'));
            flash('info', 'Please sign in to continue.');
            redirect('/login');
        }
    }

    private static function guest(): void
    {
        if (Auth::check()) {
            redirect(Auth::isAdmin() ? '/admin' : '/account');
        }
    }

    private static function admin(): void
    {
        if (!Auth::check()) {
            Session::put('intended', $_SERVER['REQUEST_URI'] ?? Url::to('/admin'));
            redirect('/login');
        }

        if (!Auth::isAdmin()) {
            throw HttpException::forbidden('You do not have access to the admin area.');
        }
    }
}
