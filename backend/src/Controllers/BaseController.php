<?php

namespace Src\Controllers;

class BaseController
{
    protected $jwtSecretKey;
    protected $jwtExpiration;

    public function __construct()
    {
        // Load configuration
        $config = require __DIR__ . '/../../config.php';

        // Load JWT configuration values
        $this->jwtSecretKey = $config['app']['jwt_secret_key'];
        $this->jwtExpiration = $config['app']['jwt_expiration'];

        // Add CORS headers
        header("Access-Control-Allow-Origin: {$this->allowedOrigin}");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");

        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit(0);
        }
    }

    protected function getJsonInput()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            return null;
        }
        return $data;
    }

    protected function getRequestHeaders()
    {
        // Check if the function getallheaders() is available
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        // Fallback for servers without getallheaders()
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) === 'HTTP_') {
                // Convert header names to a more conventional format
                $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }
}