<?php
require_once __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/Database/Database.php';

use Database\Database;

session_start();

$db = new Database();

$router = new \Core\Router($db);

require __DIR__ . '/routes.php';

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

$router->route($uri, $method);
