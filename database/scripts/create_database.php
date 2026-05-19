<?php

declare(strict_types=1);

$envPath = __DIR__ . '/../../.env';

if (! file_exists($envPath)) {
    fwrite(STDERR, ".env tidak ditemukan di {$envPath}\n");
    exit(1);
}

$env = [];
foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }
    if (! str_contains($line, '=')) {
        continue;
    }
    [$key, $value] = explode('=', $line, 2);
    $env[trim($key)] = trim($value, " \t\"'");
}

$connection = $env['DB_CONNECTION'] ?? 'mysql';

if (! in_array($connection, ['mysql', 'mariadb'], true)) {
    echo "DB_CONNECTION={$connection} - lewati pembuatan database (hanya mysql/mariadb).\n";
    exit(0);
}

$host     = $env['DB_HOST']     ?? '127.0.0.1';
$port     = $env['DB_PORT']     ?? 3306;
$username = $env['DB_USERNAME'] ?? 'root';
$password = (string) ($env['DB_PASSWORD'] ?? '');
$database = $env['DB_DATABASE'] ?? '';

if ($database === '') {
    fwrite(STDERR, "DB_DATABASE kosong di .env\n");
    exit(1);
}

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port}",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $pdo->exec(
        "CREATE DATABASE IF NOT EXISTS `{$database}` "
        . "CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    );

    echo "Database '{$database}' siap.\n";
} catch (PDOException $e) {
    fwrite(STDERR, "Gagal membuat database: {$e->getMessage()}\n");
    exit(1);
}
