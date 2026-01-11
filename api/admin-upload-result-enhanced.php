<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    $required = ['lottery', 'drawDate', 'jackpot', 'numbers', 'bonusNumbers'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $lottery = $input['lottery'];
    $drawDate = $input['drawDate'];
    $jackpot = floatval($input['jackpot']);
    $winningNumbers = array_map('intval', $input['numbers']);
    $bonusNumbers = array_map('intval', $input['bonusNumbers']);
    $publishNow = $input['publishNow'] ?? true;
    $notes = $input['notes'] ?? '';
    
    // Validate numbers
    if (count($winningNumbers) !== 5) {
        throw new Exception('Must provide exactly 5 winning numbers');
    }
    if (count($bonusNumbers) !== 2) {
        throw new Exception('Must provide exactly 2 bonus numbers');
    }
    
    // Convert lottery name to match database format
    $lotteryMap = [
        'Monday Lotto' => 'monday',
        'Wednesday Lotto' => 'wednesday', 
        'Friday Lotto' => 'friday'
    ];
    $lotteryType = $lotteryMap[$lottery] ?? strtolower($lottery);
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Insert the result
        $insertQuery = "INSERT INTO results (lottery, draw_date, winning_numbers, bonus_numbers, jackpot, status, notes, created_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $db->prepare($insertQuery);
        $stmt->execute([
            $lottery,
            $drawDate,
            json_encode($winningNumbers),
            json_encode($bonusNumbers),
            $jackpot,
            $publishNow ? 'published' : 'draft',
            $notes
        ]);
        
        $resultId = $db->lastInsertId();
        
        // Find matching entries and calculate winners
        $winnersCount = 0;
        $totalEntries = 0;
        $debugInfo = [];
        
        // Check if entries table exists
        $tableCheck = $db->prepare("SHOW TABLES LIKE 'entries'");
        $tableCheck->execute();
        
        if ($tableCheck->rowCount() > 0) {
            // Get all entries for this lottery type for the specific draw date
            $entriesQuery = "SELECT id, user_id, numbers, bonus_numbers, draw_date FROM entries 
                          WHERE lottery = ? AND DATE(draw_date) = DATE(?)
                          ORDER BY created_at DESC";
            
            $entriesStmt = $db->prepare($entriesQuery);
            $entriesStmt->execute([$lottery, $drawDate]);
            $entries = $entriesStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalEntries = count($entries);
            $debugInfo[] = "Looking for entries with lottery='$lottery' and draw_date='$drawDate'";
            $debugInfo[] = "Found $totalEntries entries";
            
            foreach ($entries as $entry) {
                $entryNumbers = json_decode($entry['numbers'], true) ?? [];
                $entryBonusNumbers = json_decode($entry['bonus_numbers'], true) ?? [];
                
                $debugInfo[] = "Entry ID {$entry['id']}: " . implode(',', $entryNumbers) . " + " . implode(',', $entryBonusNumbers);
                $debugInfo[] = "Winning: " . implode(',', $winningNumbers) . " + " . implode(',', $bonusNumbers);
                
                // Check if this entry matches the winning numbers
                $matchingNumbers = array_intersect($entryNumbers, $winningNumbers);
                $matchingBonus = array_intersect($entryBonusNumbers, $bonusNumbers);
                
                $numberMatches = count($matchingNumbers);
                $bonusMatches = count($matchingBonus);
                
                $debugInfo[] = "Matches: $numberMatches numbers, $bonusMatches bonus";
                
                // Define winning criteria (adjust as needed)
                $isWinner = false;
                $prizeLevel = '';
                
                if ($numberMatches === 5 && $bonusMatches === 2) {
                    $isWinner = true;
                    $prizeLevel = 'jackpot';
                } elseif ($numberMatches === 5 && $bonusMatches === 1) {
                    $isWinner = true;
                    $prizeLevel = 'second';
                } elseif ($numberMatches === 5) {
                    $isWinner = true;
                    $prizeLevel = 'third';
                } elseif ($numberMatches === 4) {
                    $isWinner = true;
                    $prizeLevel = 'fourth';
                } elseif ($numberMatches === 3) {
                    $isWinner = true;
                    $prizeLevel = 'fifth';
                }
                
                if ($isWinner) {
                    $debugInfo[] = "WINNER FOUND: Prize level $prizeLevel";
                    
                    // Create winners table if it doesn't exist
                    $createWinnersTable = "CREATE TABLE IF NOT EXISTS winners (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        result_id INT NOT NULL,
                        user_id INT NOT NULL,
                        entry_id INT NOT NULL,
                        prize_level VARCHAR(20) NOT NULL,
                        matching_numbers INT NOT NULL,
                        matching_bonus INT NOT NULL,
                        prize_amount DECIMAL(10,2) DEFAULT 0.00,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )";
                    $db->exec($createWinnersTable);
                    
                    // Insert winner record
                    $winnerQuery = "INSERT INTO winners (result_id, user_id, entry_id, prize_level, matching_numbers, matching_bonus, created_at)
                                   VALUES (?, ?, ?, ?, ?, ?, NOW())";
                    
                    $winnerStmt = $db->prepare($winnerQuery);
                    $winnerStmt->execute([
                        $resultId,
                        $entry['user_id'],
                        $entry['id'],
                        $prizeLevel,
                        $numberMatches,
                        $bonusMatches
                    ]);
                    
                    $winnersCount++;
                } else {
                    $debugInfo[] = "No winner - not enough matches";
                }
            }
        }
        
        // Update the result with winner count
        $updateQuery = "UPDATE results SET winners = ? WHERE id = ?";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->execute([$winnersCount, $resultId]);
        
        // Commit transaction
        $db->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Result uploaded successfully',
            'resultId' => $resultId,
            'winners' => $winnersCount,
            'totalEntries' => $totalEntries,
            'winningNumbers' => $winningNumbers,
            'bonusNumbers' => $bonusNumbers,
            'debug' => $debugInfo
        ]);
        
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollback();
        }
        throw $e;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>