<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/router.php';
require_once __DIR__ . '/vendor/autoload.php';



// Get the current request URL
$request = $_SERVER['REQUEST_URI'];

// Create a new router instance and route the request
$router = new Router();
$router->route($request);
