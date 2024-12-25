-- First, remove the foreign key constraints temporarily
ALTER TABLE farmer_crops
DROP FOREIGN KEY fk_farmercrop_farmer;

ALTER TABLE farmer_history
DROP FOREIGN KEY fk_farmerhistory_farmer;

-- Rename the name column to farmer_name if it exists
ALTER TABLE farmerlogin
CHANGE COLUMN name farmer_name VARCHAR(255) NOT NULL;

-- Add any missing columns
ALTER TABLE farmerlogin
MODIFY COLUMN email VARCHAR(255) NOT NULL,
MODIFY COLUMN password VARCHAR(255) NOT NULL,
MODIFY COLUMN phone VARCHAR(20) NOT NULL,
MODIFY COLUMN address TEXT NOT NULL,
MODIFY COLUMN state VARCHAR(100) NOT NULL,
MODIFY COLUMN district VARCHAR(100) NOT NULL;

-- Add timestamps if they don't exist
ALTER TABLE farmerlogin
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Re-add the foreign key constraints
ALTER TABLE farmer_crops
ADD CONSTRAINT fk_farmercrop_farmer 
FOREIGN KEY (farmer_id) REFERENCES farmerlogin(farmer_id) ON DELETE CASCADE;

ALTER TABLE farmer_history
ADD CONSTRAINT fk_farmerhistory_farmer 
FOREIGN KEY (farmer_id) REFERENCES farmerlogin(farmer_id) ON DELETE CASCADE;
