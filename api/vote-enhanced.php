<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';
require_once 'config/ratelimit.php';

$method = $_SERVER['REQUEST_METHOD'];

class VotingValidator {
    public static function validateNumbers($numbers, $bonusNumbers) {
        // Check array lengths
        if (!is_array($numbers) || count($numbers) !== 5) {
            return ['error' => 'Must select exactly 5 main numbers'];
        }
        
        if (!is_array($bonusNumbers) || count($bonusNumbers) !== 2) {
            return ['error' => 'Must select exactly 2 bonus numbers'];
        }
        
        // Check number ranges (1-75)
        $allNumbers = array_merge($numbers, $bonusNumbers);
        foreach ($allNumbers as $num) {
            if (!is_numeric($num) || $num < 1 || $num > 75) {
                return ['error' => 'All numbers must be between 1 and 75'];
            }
        }
        
        // Check for duplicates within main numbers
        if (count($numbers) !== count(array_unique($numbers))) {
            return ['error' => 'Duplicate numbers found in main selection'];
        }
        
        // Check for duplicates within bonus numbers
        if (count($bonusNumbers) !== count(array_unique($bonusNumbers))) {
            return ['error' => 'Duplicate numbers found in bonus selection'];
        }
        
        // Check for overlap between main and bonus
        $overlap = array_intersect($numbers, $bonusNumbers);
        if (!empty($overlap)) {
            return ['error' => 'Numbers cannot appear in both main and bonus sections'];
        }
        
        return ['valid' => true];
    }
    
    public static function validateLottery($lottery) {
        $validLotteries = ['Monday Lotto', 'Wednesday Lotto', 'Saturday Lotto', 'Sunday Lotto'];
        if (!in_array($lottery, $validLotteries)) {
            return ['error' => 'Invalid lottery type'];
        }
        return ['valid' => true];
    }
    
    public static function validateDrawDate($drawDate) {
        $date = DateTime::createFromFormat('Y-m-d', $drawDate);
        if (!$date || $date->format('Y-m-d') !== $drawDate) {
            return ['error' => 'Invalid draw date format'];
        }
        
        // Check if draw date is not in the past (allow today)
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        if ($date < $today) {
            return ['error' => 'Cannot vote for past draws'];
        }
        
        // Check if draw date is not too far in the future (max 30 days)
        $maxDate = clone $today;
        $maxDate->add(new DateInterval('P30D'));
        if ($date > $maxDate) {
            return ['error' => 'Cannot vote for draws more than 30 days in advance'];
        }
        
        return ['valid' => true];
    }
}

try {
    // Apply rate limiting
    $rateLimiter = new RateLimit();
    if (!$rateLimiter->checkLimit('voting', 10, 300)) { // 10 requests per 5 minutes
        http_response_code(429);
        echo json_encode(['error' => 'Too many voting requests. Please try again later.']);
        exit;
    }
    
    if ($method === 'POST') {
        $user = JWT::authenticate();
        
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data']);
            exit;
        }
        
        $userId = $user['id'];
        $lottery = trim($data['lottery'] ?? '');
        $numbers = $data['numbers'] ?? [];
        $bonusNumbers = $data['bonusNumbers'] ?? [];
        $drawDate = $data['drawDate'] ?? date('Y-m-d');
        
        // Validate input
        $lotteryValidation = VotingValidator::validateLottery($lottery);
        if (isset($lotteryValidation['error'])) {
            http_response_code(400);
            echo json_encode($lotteryValidation);
            exit;
        }
        
        $numbersValidation = VotingValidator::validateNumbers($numbers, $bonusNumbers);
        if (isset($numbersValidation['error'])) {
            http_response_code(400);
            echo json_encode($numbersValidation);
            exit;
        }
        
        $dateValidation = VotingValidator::validateDrawDate($drawDate);
        if (isset($dateValidation['error'])) {
            http_response_code(400);
            echo json_encode($dateValidation);
            exit;
        }
        
        $database = new Database();
        $db = $database->getConnection();
        
        // Begin transaction for data consistency
        $db->beginTransaction();
        
        try {
            // Check if user already voted for this lottery and draw date
            $checkStmt = $db->prepare("SELECT id FROM vote WHERE user_id = ? AND lottery = ? AND draw_date = ?");
            $checkStmt->execute([$userId, $lottery, $drawDate]);
            
            if ($checkStmt->rowCount() > 0) {
                $db->rollback();
                http_response_code(400);
                echo json_encode(['error' => "You have already voted for {$lottery} on {$drawDate}"]);
                exit;
            }
            
            // Sort numbers for consistency
            sort($numbers);
            sort($bonusNumbers);
            
            // Insert vote
            $stmt = $db->prepare("
                INSERT INTO vote (user_id, lottery, numbers, bonus_numbers, vote_date, draw_date, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId, 
                $lottery, 
                json_encode($numbers), 
                json_encode($bonusNumbers), 
                date('Y-m-d'), 
                $drawDate
            ]);
            
            // Log the voting activity
            $logStmt = $db->prepare("
                INSERT INTO activity_log (user_id, action, details, ip_address, created_at) 
                VALUES (?, 'vote_submitted', ?, ?, NOW())
            ");
            $logStmt->execute([
                $userId,
                json_encode([
                    'lottery' => $lottery,
                    'draw_date' => $drawDate,
                    'numbers' => $numbers,
                    'bonus_numbers' => $bonusNumbers
                ]),
                $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            
            $db->commit();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Vote submitted successfully',
                'data' => [
                    'lottery' => $lottery,
                    'draw_date' => $drawDate,
                    'numbers' => $numbers,
                    'bonus_numbers' => $bonusNumbers
                ]
            ]);
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
        
    } elseif ($method === 'GET') {
        $user = JWT::authenticate();
        
        $database = new Database();
        $db = $database->getConnection();
        
        // Get pagination parameters
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = min(50, max(10, intval($_GET['limit'] ?? 20))); // Max 50, min 10
        $offset = ($page - 1) * $limit;
        
        // Get filter parameters
        $lottery = $_GET['lottery'] ?? '';
        $dateFrom = $_GET['dateFrom'] ?? '';
        $dateTo = $_GET['dateTo'] ?? '';
        
        // Build query with filters
        $whereConditions = ['user_id = ?'];
        $params = [$user['id']];
        
        if ($lottery) {
            $whereConditions[] = 'lottery = ?';
            $params[] = $lottery;
        }
        
        if ($dateFrom) {
            $whereConditions[] = 'draw_date >= ?';
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $whereConditions[] = 'draw_date <= ?';
            $params[] = $dateTo;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Get total count
        $countStmt = $db->prepare("SELECT COUNT(*) FROM vote WHERE {$whereClause}");
        $countStmt->execute($params);
        $totalCount = $countStmt->fetchColumn();
        
        // Get votes with pagination
        $stmt = $db->prepare("
            SELECT id, lottery, numbers, bonus_numbers, vote_date, draw_date, created_at 
            FROM vote 
            WHERE {$whereClause}
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $params[] = $limit;
        $params[] = $offset;
        $stmt->execute($params);
        $votes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [];
        foreach ($votes as $vote) {
            $result[] = [
                'id' => $vote['id'],
                'lottery' => $vote['lottery'],
                'numbers' => json_decode($vote['numbers']),
                'bonusNumbers' => json_decode($vote['bonus_numbers']),
                'voteDate' => $vote['vote_date'],
                'drawDate' => $vote['draw_date'],
                'createdAt' => $vote['created_at']
            ];
        }
        
        echo json_encode([
            'votes' => $result,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $totalCount,
                'pages' => ceil($totalCount / $limit)
            ]
        ]);
        
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    error_log("Enhanced vote endpoint error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>