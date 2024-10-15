<?php

namespace Src\Controllers;

class StatusController extends BaseController
{
    public function __construct()
    {
        parent::__construct(); // Call the parent constructor to handle CORS
    }

    public function checkStatus()
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'running']);
    }
}