<?php
declare(strict_types=1);

use App\Core\Router;

require __DIR__ . '/../vendor/autoload.php';

// بارگذاری .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// error handler
$appConfig = require __DIR__ . '/../config/app.php';
if ($appConfig['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

// start session
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'cookie_secure'   => ($_SERVER['HTTPS'] ?? '') === 'on',
]);

$router = new Router();
require __DIR__ . '/../routes.php';
$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));