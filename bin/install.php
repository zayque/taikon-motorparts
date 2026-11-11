<?php

declare(strict_types=1);

/*
 * Creates the database (if missing), then loads schema.sql and seed.sql.
 * Not needed with Docker (docker-compose loads both on first start) — use it
 * there only to reset the database back to the sample catalogue.
 *
 *   php bin/install.php            # schema + sample catalogue
 *   php bin/install.php --no-seed  # schema only
 *
 * WARNING: schema.sql drops existing tables. Only run on a fresh install.
 */

if (PHP_SAPI !== 'cli') {
    exit("Run this script from the command line.\n");
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Config;

$seed = !in_array('--no-seed', $argv, true);
$force = in_array('--force', $argv, true);
$c = Config::get('database');

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $c['host'], $c['port']),
        $c['username'],
        $c['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    fwrite(STDERR, "Could not connect to MySQL: {$e->getMessage()}\nCheck DB_* values in .env\n");
    exit(1);
}

$db = str_replace('`', '', $c['database']);
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
} catch (PDOException) {
    // Non-root users (e.g. the Docker setup) can't create databases; it already exists there.
}
$pdo->exec("USE `$db`");

$hasTables = (bool) $pdo->query("SHOW TABLES LIKE 'products'")->fetchColumn();
if ($hasTables && !$force) {
    fwrite(STDOUT, "Database `$db` already has tables. This will DELETE all existing data.\nType 'yes' to continue: ");
    if (trim((string) fgets(STDIN)) !== 'yes') {
        exit("Aborted.\n");
    }
}

$files = [BASE_PATH . '/database/schema.sql'];
if ($seed) {
    $files[] = BASE_PATH . '/database/seed.sql';
}

foreach ($files as $file) {
    $pdo->exec((string) file_get_contents($file));
    echo '✓ Loaded ' . basename($file) . "\n";
}

echo "\nDone. Next: create an admin account with\n  php bin/create-admin.php\n";
