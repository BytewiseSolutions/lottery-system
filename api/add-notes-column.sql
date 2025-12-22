-- Add notes column to results table
ALTER TABLE results ADD COLUMN notes TEXT AFTER status;