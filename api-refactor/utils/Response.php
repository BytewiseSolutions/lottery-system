<?php

class Response
{
    public static function json($success, $message, $data = null, $statusCode = 200, $meta = null)
    {
        http_response_code($statusCode);

        header('Content-Type: application/json');

        $response = [
            'success' => $success,
            'message' => $message,
            'data' => $data
        ];

        if ($meta) {
            $response['meta'] = $meta;
        }

        echo json_encode($response);

        exit;
    }
}