<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

/**
 * Session-based authentication with brute-force throttling.
 */
final class Auth
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    /** @var array<string, mixed>|null|false false = not yet resolved */
    private static array|null|false $user = false;

    /** @return array<string, mixed>|null */
    public static function user(): ?array
    {
        if (self::$user === false) {
            $id = Session::get('user_id');
            self::$user = is_int($id) ? User::find($id) : null;
        }

        return self::$user;
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? null) === 'admin';
    }

    public static function tooManyAttempts(string $email): bool
    {
        $count = (int) Database::value(
            'SELECT COUNT(*) FROM login_attempts
             WHERE (email = ? OR ip_address = ?) AND attempted_at > (NOW() - INTERVAL ? MINUTE)',
            [strtolower($email), Request::ip(), self::LOCKOUT_MINUTES]
        );

        return $count >= self::MAX_ATTEMPTS;
    }

    public static function attempt(string $email, string $password): bool
    {
        $user = User::findByEmail($email);

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            Database::insert('login_attempts', [
                'email'      => strtolower($email),
                'ip_address' => Request::ip(),
            ]);

            return false;
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            User::updatePassword((int) $user['id'], $password);
        }

        Database::query('DELETE FROM login_attempts WHERE email = ?', [strtolower($email)]);
        self::login((int) $user['id']);

        return true;
    }

    public static function login(int $userId): void
    {
        Session::regenerate();
        Session::put('user_id', $userId);
        self::$user = false;
    }

    public static function logout(): void
    {
        $cart = Session::get('cart');
        Session::destroy();
        Session::start();
        if ($cart) {
            Session::put('cart', $cart);
        }
        self::$user = null;
    }
}
