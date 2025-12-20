-- Add rate limiting table
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    endpoint VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_endpoint_time (ip_address, endpoint, created_at)
);

-- Add indexes for better performance
ALTER TABLE entries ADD INDEX idx_user_date (user_id, created_at);
ALTER TABLE entries ADD INDEX idx_lottery_draw (lottery, draw_date);
ALTER TABLE results ADD INDEX idx_lottery_date (lottery, draw_date);

-- Clean up old rate limit records (run this periodically)
-- DELETE FROM rate_limits WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR);