-- Add farmer_name column if it doesn't exist
ALTER TABLE farmerlogin
ADD COLUMN farmer_name VARCHAR(255) NOT NULL AFTER farmer_id;
