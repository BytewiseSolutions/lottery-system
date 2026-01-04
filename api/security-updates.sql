-- Add role column to users table if not exists
ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(20) DEFAULT 'user';

-- Create rate_limits table
CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    endpoint VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_endpoint (ip_address, endpoint, created_at)
);

-- Create admin user (password: Admin@2024!)
INSERT INTO users (full_name, email, phone, password, role, is_active, email_verified) 
VALUES ('Administrator', 'admin@totalfreelotto.com', NULL, '$2y$10$YourHashedPasswordHere', 'admin', TRUE, TRUE)
ON DUPLICATE KEY UPDATE role = 'admin';

-- Note: Run this command to generate the admin password hash:
-- php -r "echo password_hash('Admin@2024!', PASSWORD_DEFAULT);"
-- Then replace '$2y$10$YourHashedPasswordHere' with the generated hash
