<?php

class CorsMiddleware
{
    public static function handle()
    {
        // Allow from any origin
        header("Access-Control-Allow-Origin: *");
        
        // Allow specific headers
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin");
        
        // Allow specific methods
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        
        // Allow credentials
        header("Access-Control-Allow-Credentials: true");
        
        // Set max age for preflight
        header("Access-Control-Max-Age: 3600");
        
        // Handle preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}