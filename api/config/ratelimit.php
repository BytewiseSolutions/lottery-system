<?php
class RateLimit {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function checkLimit($ip, $endpoint, $maxRequests = 60, $timeWindow = 3600) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as request_count 
            FROM rate_limits 
            WHERE ip_address = ? AND endpoint = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
        ");
        $stmt->execute([$ip, $endpoint, $timeWindow]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['request_count'] >= $maxRequests) {
            http_response_code(429);
            echo json_encode(['error' => 'Rate limit exceeded']);
            exit;
        }
        
        // Log this request
        $stmt = $this->db->prepare("INSERT INTO rate_limits (ip_address, action_type, endpoint) VALUES (?, ?, ?)");
        $stmt->execute([$ip, $endpoint, $endpoint]);
    }
}
?>