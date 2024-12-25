-- Add price_per_kg column if it doesn't exist
ALTER TABLE production_approx 
ADD COLUMN IF NOT EXISTS price_per_kg DECIMAL(10,2) DEFAULT 0.00;
