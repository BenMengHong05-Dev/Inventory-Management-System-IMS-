<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * This file allows running the application seamlessly in XAMPP
 * without having to navigate to the /public directory.
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// If the requested resource exists in public, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

require_once __DIR__ . '/public/index.php';
