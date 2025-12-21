-- Add upcoming_draws table for admin dashboard
CREATE TABLE IF NOT EXISTS upcoming_draws (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lottery VARCHAR(50) NOT NULL,
    draw_date DATETIME NOT NULL,
    jackpot VARCHAR(20) NOT NULL,
    status VARCHAR(20) DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add past_draws table for historical records
CREATE TABLE IF NOT EXISTS past_draws (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lottery VARCHAR(50) NOT NULL,
    draw_date DATETIME NOT NULL,
    jackpot VARCHAR(20) NOT NULL,
    status VARCHAR(20) DEFAULT 'completed',
    moved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add status column to results table if it doesn't exist
ALTER TABLE results ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'published';
ALTER TABLE results ADD COLUMN IF NOT EXISTS jackpot VARCHAR(20) DEFAULT '$0';
ALTER TABLE results ADD COLUMN IF NOT EXISTS winners INT DEFAULT 0;

-- Insert some sample upcoming draws
INSERT INTO upcoming_draws (lottery, draw_date, jackpot, status) VALUES
('Monday Lotto', DATE_ADD(NOW(), INTERVAL 1 DAY), '$16.00M', 'scheduled'),
('Wednesday Lotto', DATE_ADD(NOW(), INTERVAL 3 DAY), '$22.50M', 'scheduled'),
('Friday Lotto', DATE_ADD(NOW(), INTERVAL 5 DAY), '$18.75M', 'scheduled');