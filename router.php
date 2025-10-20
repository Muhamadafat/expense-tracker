<?php
// Router untuk PHP built-in server
// Ini diperlukan untuk Railway deployment

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Jika file yang diminta ada (bukan PHP), serve langsung
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Jika request ke root atau file tidak ada, serve index.php
require_once __DIR__ . '/index.php';
