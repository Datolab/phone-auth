<?php
// User.php

namespace Src\Models;

use PDO;

class User
{
    public $id;
    public $phone;
    public $telegram_id;
    public $username;

    private static function getDB()
    {
        static $db = null;
        if ($db === null) {
            $config = include __DIR__ . '/../../config/config.php';
            $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8mb4";
            $db = new PDO($dsn, $config['db']['user'], $config['db']['password']);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $db;
    }

    public static function findOrCreateByPhone($phone)
    {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE phone = :phone LIMIT 1");
        $stmt->execute(['phone' => $phone]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return self::mapDataToUser($data);
        } else {
            $stmt = $db->prepare("INSERT INTO users (phone) VALUES (:phone)");
            $stmt->execute(['phone' => $phone]);
            return self::findOrCreateByPhone($phone);
        }
    }

    public static function findOrCreateByTelegramId($telegramId, $username)
    {
        $db = self::getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE telegram_id = :telegram_id LIMIT 1");
        $stmt->execute(['telegram_id' => $telegramId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return self::mapDataToUser($data);
        } else {
            $stmt = $db->prepare("INSERT INTO users (telegram_id, username) VALUES (:telegram_id, :username)");
            $stmt->execute(['telegram_id' => $telegramId, 'username' => $username]);
            return self::findOrCreateByTelegramId($telegramId, $username);
        }
    }

    private static function mapDataToUser($data)
    {
        $user = new User();
        $user->id = $data['id'];
        $user->phone = $data['phone'];
        $user->telegram_id = $data['telegram_id'];
        $user->username = $data['username'];
        return $user;
    }
}