-- Add price_per_kg column if it doesn't exist
ALTER TABLE production_approx ADD COLUMN IF NOT EXISTS price_per_kg decimal(10,2) NOT NULL DEFAULT '0.00';

-- Update prices for existing crops
UPDATE production_approx SET price_per_kg = CASE crop
    WHEN 'arhar' THEN 85.00
    WHEN 'bajra' THEN 45.00
    WHEN 'barley' THEN 35.00
    WHEN 'cotton' THEN 75.00
    WHEN 'gram' THEN 65.00
    WHEN 'jowar' THEN 40.00
    WHEN 'jute' THEN 55.00
    WHEN 'lentil' THEN 95.00
    WHEN 'maize' THEN 50.00
    WHEN 'moong' THEN 80.00
    WHEN 'ragi' THEN 45.00
    WHEN 'rice' THEN 60.00
    WHEN 'soyabean' THEN 70.00
    WHEN 'urad' THEN 85.00
    WHEN 'wheat' THEN 55.00
    ELSE price_per_kg
END;
