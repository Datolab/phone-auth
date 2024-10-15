<?php
// OTP.php

namespace Src\Models;

use PDO;
use PDOException;

class OTP
{
    private $db;
    private $expiration;

    public function __construct()
    {
        $config = include __DIR__ . '/../../config/config.php';
        $this->db = self::getDB();
        $this->expiration = $config['app']['otp_expiration'];
    }

    private static function getDB()
    {
        static $db = null;
        if ($db === null) {
            $config = include __DIR__ . '/../../config/config.php';
            $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8mb4";
            try {
                $db = new PDO($dsn, $config['db']['user'], $config['db']['password']);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                error_log("Database connection error: " . $e->getMessage());
                throw new \Exception("Database connection error: " . $e->getMessage());
            }
        }
        return $db;
    }

    public function create($phone, $otp)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO otps (phone, otp, expires_at) VALUES (:phone, :otp, :expires_at)");
            $expiresAt = date('Y-m-d H:i:s', time() + $this->expiration);
            $stmt->execute([
                'phone' => $phone,
                'otp' => password_hash($otp, PASSWORD_BCRYPT),
                'expires_at' => $expiresAt,
            ]);
        } catch (PDOException $e) {
            error_log("Failed to create OTP: " . $e->getMessage());
            throw new \Exception("Failed to create OTP: " . $e->getMessage());
        }
    }

    public function verify($phone, $otp)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM otps WHERE phone = :phone AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
            $stmt->execute(['phone' => $phone]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data && password_verify($otp, $data['otp'])) {
                // Optionally delete the OTP after successful verification
                $deleteStmt = $this->db->prepare("DELETE FROM otps WHERE id = :id");
                $deleteStmt->execute(['id' => $data['id']]);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Failed to verify OTP: " . $e->getMessage());
            throw new \Exception("Failed to verify OTP: " . $e->getMessage());
        }
    }
}