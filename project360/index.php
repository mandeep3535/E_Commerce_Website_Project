<?php
//$basePath = '/vgarg28/project360/project360/'; // <-- YOUR base path
$basePath = '/msingh78/project360/project360/';


// Check if ?page=... is provided
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    // Get the requested path
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // If it's the base URL, show homepage
    if ($uri === $basePath || $uri === rtrim($basePath, '/')) {
        $page = 'home';
    } else {
        $uri = trim($uri, '/');
        $parts = explode('/', $uri);
        $page = end($parts);
    }

    // Handle direct calls to index
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
