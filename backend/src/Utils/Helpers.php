<?php
// Helpers.php

namespace Src\Utils;

class Helpers
{
    public static function validatePhoneNumber($phone, $countryCode)
    {
        // Convert phone country code to two-character format if necessary
        $countryCodeTwoChar = self::convertToTwoCharCountryCode($countryCode);

        // Validate phone number based on country code
        switch ($countryCodeTwoChar) {
            case 'SV': // El Salvador
                return self::validateElSalvadorPhone($phone);
            case 'US': // United States
                return self::validateUSPhone($phone);
            // Add more countries as needed
            default:
                return false; // Invalid country code or unsupported country
        }
    }

    private static function validateElSalvadorPhone($phone)
    {
        // El Salvador phone numbers are 8 digits long
        return preg_match('/^\d{8}$/', $phone);
    }

    private static function validateUSPhone($phone)
    {
        // Simple regex for US phone numbers
        return preg_match('/^\+1\d{10}$/', $phone);
    }

    public static function generateOTP($length = 6)
    {
        return str_pad(random_int(0, 999999), $length, '0', STR_PAD_LEFT);
    }

    // Helper function to convert phone country code to two-character format
    private static function convertToTwoCharCountryCode(string $phoneCountryCode): string
    {
        $countryCodeMap = [
            '+503' => 'SV', // El Salvador
            '+1' => 'US',   // United States
            // Add more mappings as needed
        ];

        return $countryCodeMap[$phoneCountryCode] ?? ''; // Return empty string if not found
    }
}