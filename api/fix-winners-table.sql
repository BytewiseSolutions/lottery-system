-- Add missing columns to winners table
ALTER TABLE winners 
ADD COLUMN result_id INT,
ADD COLUMN entry_id INT,
ADD COLUMN status VARCHAR(20) DEFAULT 'pending',
ADD COLUMN draw_date DATE,
ADD COLUMN claimed_at TIMESTAMP NULL;

-- Add foreign keys
ALTER TABLE winners 
ADD CONSTRAINT fk_winners_result FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_winners_entry FOREIGN KEY (entry_id) REFERENCES entries(id) ON DELETE CASCADE;
