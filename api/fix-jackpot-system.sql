-- Convert jackpot from VARCHAR to DECIMAL
ALTER TABLE upcoming_draws MODIFY COLUMN jackpot DECIMAL(10,2) NOT NULL DEFAULT 10.00;
