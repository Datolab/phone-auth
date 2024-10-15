<?php
// TelegramService.php

namespace Src\Services;

class TelegramService
{
    public function verifyLogin($data)
    {
        if (!isset($data['hash'])) {
            return false;
        }

        $hash = $data['hash'];
        unset($data['hash']);

        // Sort data in alphabetical order
        ksort($data);
        $data_check_string = "";
        foreach ($data as $key => $value) {
            $data_check_string .= $key . '=' . $value . "\n";
        }
        $data_check_string = rtrim($data_check_string, "\n");

        // Generate secret key
        $secret = hash('sha256', getenv('TELEGRAM_BOT_TOKEN'), true);

        // Calculate HMAC-SHA256
        $hash_calculated = hash_hmac('sha256', $data_check_string, $secret);

        return hash_equals($hash_calculated, $hash);
    }
}