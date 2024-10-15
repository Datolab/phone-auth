<?php
// SMSService.php

namespace Src\Services;

use Twilio\Rest\Client;
use Twilio\Exceptions\RestException;

class SMSService
{
    private $client;
    private $serviceSid;
    private $verification;

    public function __construct()
    {
        // Load Twilio credentials from the configuration file
        $config = include __DIR__ . '/../../config/config.php'; // Adjust the path as necessary

        $accountSid = $config['twilio']['account_sid'];
        $authToken = $config['twilio']['auth_token'];
        $this->serviceSid = $config['twilio']['service_sid']; // Add this to your config

        // Debugging: Log the credentials
        error_log("Twilio Account SID: " . $accountSid);
        error_log("Twilio Auth Token: " . $authToken);
        error_log("Twilio Phone Number: " . $this->from);

        // Initialize Twilio client with error handling
        try {
            $this->client = new Client($accountSid, $authToken);
        } catch (RestException $e) {
            error_log("Failed to initialize Twilio client: " . $e->getMessage());
            throw new \Exception("Failed to initialize Twilio client: " . $e->getMessage());
        }
    }

    public function sendOTP($to, $otp)
    {
        try {

            $message = $this->client->verify->v2->services($this->serviceSid)
                ->verifications
                ->create($to, "sms");


            return $message->sid; // Return message SID if needed
        } catch (RestException $e) {
            error_log("Failed to send OTP: " . $e->getMessage());
            throw new \Exception("Failed to send OTP: " . $e->getMessage());
        }
    }

    public function verifyOTP($to, $otp)
    {
        try {

            $message = $this->client->verify->v2->services($this->serviceSid)
                ->verificationChecks
                ->create([
                    "to" => $to,
                    "code" => $otp
                ]
             );

             return $message->sid;
            
        } catch (RestException $e) {
            error_log("Failed to verify OTP: " . $e->getMessage());
            throw new \Exception("Failed to verify OTP: " . $e->getMessage());
        }
    }
}