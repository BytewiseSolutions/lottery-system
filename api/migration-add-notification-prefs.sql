-- Add notification_enabled column to user table
ALTER TABLE user ADD COLUMN IF NOT EXISTS notification_enabled TINYINT(1) DEFAULT 1;

-- Add index for better performance
CREATE INDEX IF NOT EXISTS idx_notification_enabled ON user(notification_enabled);
