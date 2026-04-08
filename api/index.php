<?php

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

// ── Setup writable storage in /tmp (Vercel filesystem is read-only) ──────────
$tmpStorage = '/tmp/laravel-storage';

$dirsToCreate = [
    $tmpStorage . '/app/public',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/testing',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/logs',
];

foreach ($dirsToCreate as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// ── Copy SQLite database to /tmp if not already there ────────────────────────
$dbSource = __DIR__ . '/../database/database.sqlite';
$dbDest   = '/tmp/database.sqlite';

if (file_exists($dbSource) && !file_exists($dbDest)) {
    copy($dbSource, $dbDest);
}

// ── Boot Laravel ─────────────────────────────────────────────────────────────
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath($tmpStorage);

// Force view compiled path to writable directory
$app->bind('path.storage', fn() => $tmpStorage);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
