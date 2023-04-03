<?php

use App\Router;

require __DIR__ . '/../bootstrap/init.php';

$router = new Router();
$router->load($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
