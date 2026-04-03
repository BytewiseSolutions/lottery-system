<?php
header('Content-Type: application/json');
require_once 'config/cors.php';
require_once 'config/database.php';

class VotingAnalytics {
    private $db;
    private $cacheDir;
    
    public function __construct($database) {
        $this->db = $database;
        $this->cacheDir = __DIR__ . '/cache/';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    private function getCacheKey($lottery, $voteDate) {
        return 'leading_numbers_' . md5($lottery . '_' . $voteDate);
    }
    
    private function getCache($key) {
        $file = $this->cacheDir . $key . '.json';
        if (file_exists($file) && (time() - filemtime($file)) < 300) { // 5 minutes cache
            return json_decode(file_get_contents($file), true);
        }
        return null;
    }
    
    private function setCache($key, $data) {
        $file = $this->cacheDir . $key . '.json';
        file_put_contents($file, json_encode($data));
    }
    
    public function getLeadingNumbers($lottery, $voteDate) {
        $cacheKey = $this->getCacheKey($lottery, $voteDate);
        $cached = $this->getCache($cacheKey);
        
        if ($cached) {
            return $cached;
        }
        
        $numberCounts = [];
        $bonusCounts = [];
        
        // Get user votes
        $stmt = $this->db->prepare("
            SELECT numbers, bonus_numbers 
            FROM vote 
            WHERE lottery = ? AND draw_date = ?
        ");
        $stmt->execute([$lottery, $voteDate]);
        $userVotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($userVotes as $vote) {
            $numbers = json_decode($vote['numbers'], true) ?: [];
            $bonusNumbers = json_decode($vote['bonus_numbers'], true) ?: [];
            
            foreach ($numbers as $num) {
                $numberCounts[$num] = ($numberCounts[$num] ?? 0) + 1;
            }
            
            foreach ($bonusNumbers as $num) {
                $bonusCounts[$num] = ($bonusCounts[$num] ?? 0) + 1;
            }
        }
        
        // Get admin allocated votes
        $stmt = $this->db->prepare("
            SELECT numbers, bonus_numbers, allocated_votes, voting_data, total_votes 
            FROM admin_vote 
            WHERE lottery = ? AND draw_date = ?
        ");
        $stmt->execute([$lottery, $voteDate]);
        $adminVotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($adminVotes as $vote) {
            $votingData = $vote['voting_data'] ? json_decode($vote['voting_data'], true) : null;
            
            if ($votingData && isset($votingData['mainNumberVotes']) && isset($votingData['bonusNumberVotes'])) {
                // New format: use individual vote amounts
                foreach ($votingData['mainNumberVotes'] as $number => $votes) {
                    $numberCounts[$number] = ($numberCounts[$number] ?? 0) + $votes;
                }
                
                foreach ($votingData['bonusNumberVotes'] as $number => $votes) {
                    $bonusCounts[$number] = ($bonusCounts[$number] ?? 0) + $votes;
                }
            } else {
                // Legacy format: distribute votes evenly
                $numbers = json_decode($vote['numbers'], true) ?: [];
                $bonusNumbers = json_decode($vote['bonus_numbers'], true) ?: [];
                $allocatedVotes = $vote['allocated_votes'] ?: $vote['total_votes'] ?: 0;
                $totalNumbers = count($numbers) + count($bonusNumbers);
                $votesPerNumber = $totalNumbers > 0 ? floor($allocatedVotes / $totalNumbers) : 0;
                
                foreach ($numbers as $num) {
                    $numberCounts[$num] = ($numberCounts[$num] ?? 0) + $votesPerNumber;
                }
                
                foreach ($bonusNumbers as $num) {
                    $bonusCounts[$num] = ($bonusCounts[$num] ?? 0) + $votesPerNumber;
                }
            }
        }
        
        // Sort by vote count (descending)
        arsort($numberCounts);
        arsort($bonusCounts);
        
        $result = [
            'lottery' => $lottery,
            'draw_date' => $voteDate,
            'total_user_votes' => count($userVotes),
            'total_admin_allocations' => count($adminVotes),
            'section1' => array_map(function($num, $count) {
                return ['number' => (int)$num, 'votes' => (int)$count];
            }, array_keys($numberCounts), array_values($numberCounts)),
            'section2' => array_map(function($num, $count) {
                return ['number' => (int)$num, 'votes' => (int)$count];
            }, array_keys($bonusCounts), array_values($bonusCounts)),
            'top_combination' => [
                'main_numbers' => array_slice(array_keys($numberCounts), 0, 5),
                'bonus_numbers' => array_slice(array_keys($bonusCounts), 0, 2)
            ],
            'statistics' => [
                'total_main_votes' => array_sum($numberCounts),
                'total_bonus_votes' => array_sum($bonusCounts),
                'unique_main_numbers' => count($numberCounts),
                'unique_bonus_numbers' => count($bonusCounts)
            ],
            'generated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->setCache($cacheKey, $result);
        return $result;
    }
    
    public function getVotingTrends($lottery, $days = 7) {
        $trends = [];
        $endDate = new DateTime();
        $startDate = clone $endDate;
        $startDate->sub(new DateInterval("P{$days}D"));
        
        $stmt = $this->db->prepare("
            SELECT 
                draw_date,
                COUNT(*) as vote_count,
                COUNT(DISTINCT user_id) as unique_voters
            FROM vote 
            WHERE lottery = ? AND draw_date BETWEEN ? AND ?
            GROUP BY draw_date
            ORDER BY draw_date DESC
        ");
        
        $stmt->execute([
            $lottery, 
            $startDate->format('Y-m-d'), 
            $endDate->format('Y-m-d')
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

try {
    $lottery = $_GET['lottery'] ?? '';
    $voteDate = $_GET['voteDate'] ?? date('Y-m-d');
    $includeTrends = isset($_GET['trends']) && $_GET['trends'] === 'true';
    
    if (empty($lottery)) {
        http_response_code(400);
        echo json_encode(['error' => 'Lottery parameter required']);
        exit;
    }
    
    // Validate lottery type
    $validLotteries = ['Monday Lotto', 'Wednesday Lotto', 'Saturday Lotto', 'Sunday Lotto'];
    if (!in_array($lottery, $validLotteries)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid lottery type']);
        exit;
    }
    
    // Validate date format
    $date = DateTime::createFromFormat('Y-m-d', $voteDate);
    if (!$date || $date->format('Y-m-d') !== $voteDate) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid date format. Use YYYY-MM-DD']);
        exit;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    $analytics = new VotingAnalytics($db);
    $result = $analytics->getLeadingNumbers($lottery, $voteDate);
    
    if ($includeTrends) {
        $result['trends'] = $analytics->getVotingTrends($lottery, 7);
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log("Enhanced leading numbers error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>