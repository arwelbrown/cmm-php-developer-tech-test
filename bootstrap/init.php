<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';
// Load environment variables

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if ($_ENV['APP_ENV'] == 'dev') {
    // Display errors in dev
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
