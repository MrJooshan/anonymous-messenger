<?php
use App\Controllers\AuthController;
use App\Controllers\MessageController;
use App\Controllers\DashboardController;

/** @var App\Core\Router $router */

$router->get('/', [MessageController::class, 'showSendForm']);
$router->post('/', [MessageController::class, 'send']);

$router->get('/login',  [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register',  [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->get('/dashboard', [DashboardController::class, 'index']);