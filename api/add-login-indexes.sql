-- Add indexes for faster login queries
ALTER TABLE users ADD INDEX idx_email (email);
ALTER TABLE users ADD INDEX idx_phone (phone);
ALTER TABLE users ADD INDEX idx_is_active (is_active);
