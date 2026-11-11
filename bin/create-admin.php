<?php

declare(strict_types=1);

/*
 * Create an admin user, or promote/reset an existing one.
 *
 *   php bin/create-admin.php
 *   php bin/create-admin.php --name="Ahmad" --email=owner@example.com --password=...
 */

if (PHP_SAPI !== 'cli') {
    exit("Run this script from the command line.\n");
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;
use App\Models\User;

$opts = getopt('', ['name:', 'email:', 'password:']);

$ask = static function (string $label, bool $secret = false): string {
    echo $label . ': ';
    if ($secret && DIRECTORY_SEPARATOR === '/') {
        system('stty -echo');
        $value = trim((string) fgets(STDIN));
        system('stty echo');
        echo "\n";
        return $value;
    }
    return trim((string) fgets(STDIN));
};

$name = $opts['name'] ?? $ask('Name');
$email = strtolower(trim($opts['email'] ?? $ask('Email')));
$password = $opts['password'] ?? $ask('Password (min 8 chars)', true);

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
    fwrite(STDERR, "Invalid input: name required, valid email, password at least 8 characters.\n");
    exit(1);
}

$existing = User::findByEmail($email);

if ($existing) {
    Database::update('users', (int) $existing['id'], [
        'name'          => $name,
        'role'          => 'admin',
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    ]);
    echo "✓ Updated $email — now an admin with the new password.\n";
} else {
    User::create(['name' => $name, 'email' => $email, 'phone' => '', 'password' => $password, 'role' => 'admin']);
    echo "✓ Admin $email created. Sign in at /login, then open /admin.\n";
}
