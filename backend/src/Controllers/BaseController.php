<?php

namespace Src\Controllers;

class BaseController
{
    public function __construct()
    {
        // Add CORS headers
        header("Access-Control-Allow-Origin: http://localhost:8001");
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
}