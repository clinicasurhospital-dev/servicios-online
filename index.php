<?php

/**
 * Laravel IIS / Apache root forwarder
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? ''
);

$filePath = __DIR__ . '/public' . $uri;

if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'json' => 'application/json',
    ];

    if (isset($mimeTypes[$extension])) {
        header('Content-Type: ' . $mimeTypes[$extension]);
    }
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}

require_once __DIR__ . '/public/index.php';
