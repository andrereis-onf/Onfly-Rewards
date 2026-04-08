<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ── Writable storage in /tmp (Vercel filesystem is read-only) ────────────────
$tmpStorage = '/tmp/laravel-storage';

foreach ([
    "$tmpStorage/app/public",
    "$tmpStorage/framework/cache/data",
    "$tmpStorage/framework/sessions",
    "$tmpStorage/framework/testing",
    "$tmpStorage/framework/views",
    "$tmpStorage/logs",
] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// ── Copy SQLite database to /tmp ─────────────────────────────────────────────
$dbSource = __DIR__ . '/../database/database.sqlite';
$dbDest   = '/tmp/database.sqlite';

if (file_exists($dbSource) && !file_exists($dbDest)) {
    copy($dbSource, $dbDest);
}

// ── Bootstrap Laravel (same as public/index.php) ─────────────────────────────
require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath($tmpStorage);

$app->handleRequest(Request::capture());
