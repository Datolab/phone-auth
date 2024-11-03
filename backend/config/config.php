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
        'jwt_secret_key' => getenv('JWT_SECRET_KEY') ?: 'c085427dd4913411688c48a04415156dc8ed95654bf7c158a847604239056ad0be090fce3fda5cb0b5d883ed987d0b5a9b564a57ab2ef78b6d8e83cd5a3a87ed6f4a8645408c1b60a1269f3225ca793b9108a41a33599d43a11e9fe07357c96adf599b8e2372e7542858574afba1ecc042ec7cb68f490c77b303a5114c68725a31055a70630a2954bef4dc392e37feb9c5db7c7592d04bb7e07405a77fbbe0918bf9d07de763048b562b17c35567936ffd20da718e6a1f24e77f9c71cc9810c024a9a99c0b96643930b5a0b30323ad74d2fdc70167a0e4915bfac9d642d86c6824d4e13efcaf937dd13e483d22ab53be3de29ad46d9f036d60ce9495e000016b', // JWT secret key
        'jwt_expiration' => getenv('JWT_EXPIRATION') ?: 3600, // JWT token expiration in seconds
        'allowed_origin' => getenv('ALLOWED_ORIGIN') ?: 'http://localhost:8001', // Default to localhost
    ],
];