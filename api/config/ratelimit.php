<?php
class RateLimit {
    private $cacheDir;
    
    public function __construct() {
        $this->cacheDir = __DIR__ . '/../cache/ratelimit/';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * Check if request is within rate limit
     * @param string $key - Unique identifier (e.g., 'voting', 'login')
     * @param int $maxRequests - Maximum requests allowed
     * @param int $timeWindow - Time window in seconds
     * @return bool - True if within limit, false if exceeded
     */
    public function checkLimit($key, $maxRequests, $timeWindow) {
        $clientId = $this->getClientId();
        $cacheKey = $key . '_' . $clientId;
        $file = $this->cacheDir . $cacheKey . '.json';
        
        $now = time();
        $requests = [];
        
        // Load existing requests
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            $requests = $data['requests'] ?? [];
        }
        
        // Remove old requests outside time window
        $requests = array_filter($requests, function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        // Check if limit exceeded
        if (count($requests) >= $maxRequests) {
            return false;
        }
        
        // Add current request
        $requests[] = $now;
        
        // Save updated requests
        file_put_contents($file, json_encode(['requests' => $requests]));
        
        return true;
    }
    
    /**
     * Get remaining requests for a key
     */
    public function getRemainingRequests($key, $maxRequests, $timeWindow) {
        $clientId = $this->getClientId();
        $cacheKey = $key . '_' . $clientId;
        $file = $this->cacheDir . $cacheKey . '.json';
        
        if (!file_exists($file)) {
            return $maxRequests;
        }
        
        $now = time();
        $data = json_decode(file_get_contents($file), true);
        $requests = $data['requests'] ?? [];
        
        // Remove old requests
        $requests = array_filter($requests, function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        return max(0, $maxRequests - count($requests));
    }
    
    /**
     * Get time until rate limit resets
     */
    public function getResetTime($key, $timeWindow) {
        $clientId = $this->getClientId();
        $cacheKey = $key . '_' . $clientId;
        $file = $this->cacheDir . $cacheKey . '.json';
        
        if (!file_exists($file)) {
            return 0;
        }
        
        $data = json_decode(file_get_contents($file), true);
        $requests = $data['requests'] ?? [];
        
        if (empty($requests)) {
            return 0;
        }
        
        $oldestRequest = min($requests);
        $resetTime = $oldestRequest + $timeWindow;
        
        return max(0, $resetTime - time());
    }
    
    /**
     * Clear rate limit for a key (admin function)
     */
    public function clearLimit($key, $clientId = null) {
        if ($clientId === null) {
            $clientId = $this->getClientId();
        }
        
        $cacheKey = $key . '_' . $clientId;
        $file = $this->cacheDir . $cacheKey . '.json';
        
        if (file_exists($file)) {
            unlink($file);
        }
    }
    
    /**
     * Get unique client identifier
     */
    private function getClientId() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Include user ID if authenticated
        $userId = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            try {
                $token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
                $decoded = JWT::decode($token);
                $userId = $decoded['id'] ?? '';
            } catch (Exception $e) {
                // Ignore JWT errors for rate limiting
            }
        }
        
        return md5($ip . $userAgent . $userId);
    }
    
    /**
     * Clean up old cache files
     */
    public function cleanup() {
        $files = glob($this->cacheDir . '*.json');
        $now = time();
        
        foreach ($files as $file) {
            if (($now - filemtime($file)) > 3600) { // Remove files older than 1 hour
                unlink($file);
            }
        }
    }
}
?>