<?php
// AuthController.php

namespace Src\Controllers;

use Src\Services\SMSService;
use Src\Models\User;
use Src\Models\OTP;
use Src\Utils\Helpers;
use Src\Controllers\BaseController;

class AuthController extends BaseController
{
    public function __construct()
    {
        parent::__construct(); // Call the parent constructor to handle CORS
    }

    public function sendSMS()
    {
        // Get the raw POST data
        $data = $this->getJsonInput();
        if ($data === null) return; // Exit if JSON is invalid

        // Log the received data for debugging
        error_log("Received POST data: " . print_r($data, true));

        // Get phone number and country code from the decoded JSON
        $phone = $data['phone'] ?? null;
        $countryCode = $data['country_code'] ?? null; // Country code in phone format (e.g., "+503")

        // Check if both phone and country code are provided
        if (!$phone || !$countryCode) {
            error_log("Missing phone number or country code");
            http_response_code(400);
            echo json_encode(['error' => 'Missing phone number or country code']);
            return;
        }

        $fullPhone = $countryCode . $phone;

        // Convert country code from phone format to two-character format
        // $countryCodeTwoChar = $this->convertToTwoCharCountryCode($countryCode);

        // Validate phone number using the two-character country code
        if (!Helpers::validatePhoneNumber($phone, $countryCode)) {
            error_log("Invalid phone number: " . $fullPhone);
            http_response_code(400);
            echo json_encode(['error' => 'Invalid phone number']);
            return;
        }

        // Check if the user exists
        $user = User::findOrCreateByPhone($fullPhone);
        if (!$user) {
            if (!$user) {
                error_log("Failed to create user for phone: " . $fullPhone);
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create user']);
                return;
            }
        }

        // Generate OTP
        $otpCode = Helpers::generateOTP();
        $otp = new OTP();
        $otp->create($fullPhone, $otpCode);

        // Send OTP via SMS
        $smsService = new SMSService();
        $smsService->sendOTP($fullPhone, $otpCode);

        echo json_encode(['message' => 'OTP sent successfully']);
    }

    public function verifyOTP()
    {
        $data = $this->getJsonInput();
        if ($data === null) return;

        $phone = $data['phone'] ?? null;
        $otpCode = $data['otp'] ?? null;

        if (!$phone || !$otpCode) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing parameters']);
            return;
        }

        $smsService = new SMSService();
        if ($smsService->verifyOTP($phone, $otpCode)) {
            $user = User::findOrCreateByPhone($phone);

            $issuedAt = time();
            $expirationTime = $issuedAt + $this->jwtExpiration;
            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'sub' => $user->id,
                'phone' => $phone
            ];

            $jwt = JWT::encode($payload, $this->jwtSecretKey, 'HS256');

            echo json_encode(['token' => $jwt, 'expires_in' => $this->jwtExpiration]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired OTP']);
        }
    }

    public function validateToken()
    {
        // Get request headers using the BaseController's getRequestHeaders method
        $headers = $this->getRequestHeaders();
        
        // Check if the Authorization header is present
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Authorization header missing']);
            exit;
        }
    
        // Extract the token from "Bearer <token>"
        list($type, $token) = explode(" ", $headers['Authorization'], 2);
        if (strcasecmp($type, 'Bearer') !== 0 || empty($token)) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid authorization format']);
            exit;
        }
    
        // Decode and validate the token
        try {
            $decoded = \Firebase\JWT\JWT::decode($token, $this->jwtSecretKey, ['HS256']);
            return (array) $decoded; // Convert to an array for easy access
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid or expired token', 'message' => $e->getMessage()]);
            exit;
        }
    }
}