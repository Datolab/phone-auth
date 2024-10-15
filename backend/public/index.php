<?php
// index.php

require_once __DIR__ . '/../vendor/autoload.php';

use Src\Controllers\AuthController;
use Src\Controllers\TelegramController;
use Src\Controllers\StatusController;

// Define routes with templates
$routes = [
    '/auth/sms' => [AuthController::class, 'sendSMS'],
    '/auth/verify' => [AuthController::class, 'verifyOTP'],
    '/auth/telegram' => [TelegramController::class, 'handleTelegramLogin'],
    '/status' => [StatusController::class, 'checkStatus'],
];

// Simple routing logic
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

foreach ($routes as $route => $handler) {
    if ($uri === $route) {
        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();
        $controller->$method();
        exit;
    }
}

// If no route matches
http_response_code(404);
echo 'Not Found';