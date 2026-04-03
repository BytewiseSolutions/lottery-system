<?php
header('Content-Type: application/json');
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        $user = JWT::authenticate();
        
        $data = json_decode(file_get_contents('php://input'), true);
        $type = $data['type'] ?? 'main'; // 'main' or 'bonus'
        $excludeNumbers = $data['excludeNumbers'] ?? []; // Numbers to exclude (for bonus)
        
        // Validate exclude numbers
        if (!is_array($excludeNumbers)) {
            $excludeNumbers = [];
        }
        
        // Generate available numbers (1-75)
        $allNumbers = range(1, 75);
        $availableNumbers = array_diff($allNumbers, $excludeNumbers);
        $availableNumbers = array_values($availableNumbers); // Re-index array
        
        if ($type === 'main') {
            // Generate 5 main numbers
            if (count($availableNumbers) < 5) {
                http_response_code(400);
                echo json_encode(['error' => 'Not enough available numbers for main selection']);
                exit;
            }
            
            $selectedNumbers = [];
            for ($i = 0; $i < 5; $i++) {
                $randomIndex = random_int(0, count($availableNumbers) - 1);
                $selectedNumbers[] = $availableNumbers[$randomIndex];
                // Remove selected number from available pool
                array_splice($availableNumbers, $randomIndex, 1);
            }
            
            // Sort numbers in ascending order
            sort($selectedNumbers);
            
            echo json_encode([
                'success' => true,
                'numbers' => $selectedNumbers,
                'type' => 'main'
            ]);
            
        } elseif ($type === 'bonus') {
            // Generate 2 bonus numbers
            if (count($availableNumbers) < 2) {
                http_response_code(400);
                echo json_encode(['error' => 'Not enough available numbers for bonus selection']);
                exit;
            }
            
            $selectedNumbers = [];
            for ($i = 0; $i < 2; $i++) {
                $randomIndex = random_int(0, count($availableNumbers) - 1);
                $selectedNumbers[] = $availableNumbers[$randomIndex];
                // Remove selected number from available pool
                array_splice($availableNumbers, $randomIndex, 1);
            }
            
            // Sort numbers in ascending order
            sort($selectedNumbers);
            
            echo json_encode([
                'success' => true,
                'numbers' => $selectedNumbers,
                'type' => 'bonus'
            ]);
            
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid type. Must be "main" or "bonus"']);
        }
        
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    error_log("Quick pick error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>