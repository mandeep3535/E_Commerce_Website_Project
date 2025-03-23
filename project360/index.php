<?php
// Normalize the request URI
$request_uri = $_SERVER['REQUEST_URI'];

// Redirect if URI is just base or index.php (to make the URL explicit)
if ($request_uri === '/project360/project360/' || $request_uri === '/project360/project360/index.php') {
    header("Location: index.php?page=home");
    exit;
}

// Check if ?page=... is provided
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    // Get the requested path
    $uri = parse_url($request_uri, PHP_URL_PATH);
    $uri = trim($uri, '/');
    $parts = explode('/', $uri);
    $page = end($parts);

    // Handle empty, 'index', or 'index.php' as 'home'
    if ($page === '' || $page === 'index' || $page === 'index.php') {
        $page = 'home';
    }
}

$filepath = "$page.php";

// Security check
if (preg_match('/^[a-zA-Z0-9_-]+$/', $page) && file_exists($filepath)) {
    include $filepath;
} else {
    include "404.php";
}
