<?php
// router.php for PHP built-in development server
$parsedUrl = parse_url($_SERVER["REQUEST_URI"]);
$path = ltrim($parsedUrl["path"], '/');

// If the requested path points to an actual file on disk, let the built-in server serve it
if ($path !== '' && file_exists($path) && is_file($path)) {
    return false;
}

// Otherwise, route all requests through CodeIgniter's front controller (index.php)
$_SERVER['SCRIPT_NAME'] = '/index.php';
include_once 'index.php';
