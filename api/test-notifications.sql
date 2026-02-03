-- Sample notifications for testing
-- Replace USER_ID with actual user ID from your database

-- Test notification 1: Win notification
INSERT INTO notification (user_id, sent_by, title, message, type, is_read) 
VALUES (1, 1, 'Congratulations! You Won!', 'You won $1,000,000.00 in the Monday Lotto draw!', 'success', 0);

-- Test notification 2: Results published
INSERT INTO notification (user_id, sent_by, title, message, type, is_read) 
VALUES (1, 1, 'Results Published', 'Results for Wednesday Lotto on Jan 15, 2024 are now available.', 'info', 0);

-- Test notification 3: Payment processed
INSERT INTO notification (user_id, sent_by, title, message, type, is_read) 
VALUES (1, 1, 'Payment Processed', 'Your prize of $1,000,000.00 has been paid!', 'success', 1);

-- Test notification 4: Warning
INSERT INTO notification (user_id, sent_by, title, message, type, is_read) 
VALUES (1, 1, 'Account Update Required', 'Please update your payment information to receive your winnings.', 'warning', 0);

-- Verify notifications
SELECT * FROM notification WHERE user_id = 1 ORDER BY created_at DESC;

-- Check unread count
SELECT COUNT(*) as unread_count FROM notification WHERE user_id = 1 AND is_read = 0;
