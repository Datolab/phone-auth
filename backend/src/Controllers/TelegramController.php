<?php
// TelegramController.php

namespace Src\Controllers;

use Src\Services\TelegramService;
use Src\Models\User;
use Src\Utils\Helpers;

class TelegramController
{
    public function handleTelegramLogin()
    {
        // Get Telegram data from GET parameters
        $data = $_GET;

        $telegramService = new TelegramService();
        if ($telegramService->verifyLogin($data)) {
            $userId = $data['id'];
            $username = $data['username'] ?? 'unknown';

            // Find or create user
            $user = User::findOrCreateByTelegramId($userId, $username);

            // Authenticate user (e.g., create session or JWT)
            session_start();
            $_SESSION['user_id'] = $user->id;

            // Redirect to dashboard or home
            header('Location: ' . getenv('APP_BASE_URL') . '/dashboard');
        } else {
            http_response_code(403);
            echo 'Unauthorized';
        }
    }
}