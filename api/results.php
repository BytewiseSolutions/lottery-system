<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

$isAdmin = false;
$headers = getallheaders();
if (isset($headers['Authorization'])) {
    try {
        $user = JWT::authenticate();
        $isAdmin = isset($user['role']) && $user['role'] === 'admin';
    } catch (Exception $e) {

    }
}

if ($method === 'GET') {
    try {
        // Admin sees all results, public sees only published
        if ($isAdmin) {
            $query = "SELECT * FROM results ORDER BY draw_date DESC";
        } else {
            $query = "SELECT * FROM results WHERE status = 'published' ORDER BY draw_date DESC";
        }
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format results for frontend
        $formattedResults = [];
        foreach ($results as $result) {
            $formattedResults[] = [
                'id' => $result['id'],
                'lottery' => $result['lottery'],
                'drawDate' => $result['draw_date'],
                'numbers' => json_decode($result['winning_numbers']),
                'bonusNumbers' => json_decode($result['bonus_numbers']),
                'jackpot' => $result['jackpot'],
                'winners' => $result['winners'],
                'status' => $result['status'] ?? 'published',
                'notes' => $result['notes'] ?? '',
                'updatedAt' => $result['created_at']
            ];
        }
        
        echo json_encode($formattedResults);
        
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch results']);
    }
} elseif ($method === 'PUT') {
    // Only admin can update results
    if (!$isAdmin) {
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }

    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Result ID is required']);
            exit;
        }

        // Validate required fields
        if (!isset($data['lottery']) || !isset($data['drawDate']) || !isset($data['jackpot'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        // Prepare update query
        $query = "UPDATE results SET 
                    lottery = :lottery,
                    draw_date = :drawDate,
                    jackpot = :jackpot,
                    status = :status";

        // Add numbers if provided
        if (isset($data['numbers']) && is_array($data['numbers'])) {
            $query .= ", winning_numbers = :numbers";
        }
        if (isset($data['bonusNumbers']) && is_array($data['bonusNumbers'])) {
            $query .= ", bonus_numbers = :bonusNumbers";
        }

        $query .= " WHERE id = :id";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':lottery', $data['lottery']);
        $stmt->bindParam(':drawDate', $data['drawDate']);
        $stmt->bindParam(':jackpot', $data['jackpot']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':id', $id);

        if (isset($data['numbers']) && is_array($data['numbers'])) {
            $numbers = json_encode($data['numbers']);
            $stmt->bindParam(':numbers', $numbers);
        }
        if (isset($data['bonusNumbers']) && is_array($data['bonusNumbers'])) {
            $bonusNumbers = json_encode($data['bonusNumbers']);
            $stmt->bindParam(':bonusNumbers', $bonusNumbers);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Result updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update result']);
        }

    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $exception->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>