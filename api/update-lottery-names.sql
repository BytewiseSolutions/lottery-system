-- Update lottery names from abbreviated to full day names
USE lottery_db;

-- Update entries table
UPDATE entries SET lottery = 'Monday Lotto' WHERE lottery = 'Mon Lotto';
UPDATE entries SET lottery = 'Wednesday Lotto' WHERE lottery = 'Wed Lotto';
UPDATE entries SET lottery = 'Friday Lotto' WHERE lottery = 'Fri Lotto';

-- Update results table
UPDATE results SET lottery = 'Monday Lotto' WHERE lottery = 'Mon Lotto';
UPDATE results SET lottery = 'Wednesday Lotto' WHERE lottery = 'Wed Lotto';
UPDATE results SET lottery = 'Friday Lotto' WHERE lottery = 'Fri Lotto';

-- Update winners table
UPDATE winners SET lottery = 'Monday Lotto' WHERE lottery = 'Mon Lotto';
UPDATE winners SET lottery = 'Wednesday Lotto' WHERE lottery = 'Wed Lotto';
UPDATE winners SET lottery = 'Friday Lotto' WHERE lottery = 'Fri Lotto';