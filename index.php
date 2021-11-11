<?php
require_once __DIR__ . 'Framework';

$router = new router();

$routes = require_once __DIR__ . 'routes.php';
$routes($router);

print $router->dispatch();