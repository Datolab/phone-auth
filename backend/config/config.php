<?php
// config.php

return [
    'db' => [
        'host' => getenv('DB_HOST'),
        'dbname' => getenv('DB_NAME'),
        'user' => getenv('DB_USER'),
        'password' => getenv('DB_PASSWORD'),
    ],
    'twilio' => [
        'account_sid' => getenv('TWILIO_ACCOUNT_SID'),
        'service_sid' => getenv('TWILIO_SERVICE_SID'),
        'auth_token' => getenv('TWILIO_AUTH_TOKEN'),
        'phone_number' => getenv('TWILIO_PHONE_NUMBER'),
    ],
    'telegram' => [
        'bot_name' => getenv('TELEGRAM_BOT_NAME'),
        'bot_token' => getenv('TELEGRAM_BOT_TOKEN'),
        'login_url' => getenv('TELEGRAM_LOGIN_URL'),
    ],
    'app' => [
        'base_url' => getenv('APP_BASE_URL'),
        'otp_expiration' => 300, // seconds
    ],
];