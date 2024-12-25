-- Check and fix stock quantity issues
UPDATE farmer_crops_trade 
SET Crop_quantity = ABS(Crop_quantity) 
WHERE Crop_quantity < 0;

-- Remove any negative quantities
UPDATE cart 
SET quantity = ABS(quantity) 
WHERE quantity < 0;

-- Delete duplicate entries in farmer_crops_trade
DELETE t1 FROM farmer_crops_trade t1
INNER JOIN farmer_crops_trade t2 
WHERE t1.id > t2.id 
AND t1.farmer_fkid = t2.farmer_fkid 
AND t1.Trade_crop = t2.Trade_crop;

-- Useful diagnostic queries
-- 1. Check cart items
SELECT c.*, cr.Crop_name, f.farmer_name, fct.Crop_quantity as stock
FROM cart c
JOIN crops cr ON c.crop_id = cr.Crop_id
JOIN farmerlogin f ON c.farmer_id = f.farmer_id
LEFT JOIN farmer_crops_trade fct ON c.farmer_id = fct.farmer_fkid 
AND LOWER(TRIM(fct.Trade_crop)) = LOWER(TRIM(cr.Crop_name));

-- 2. Check stock levels
SELECT fct.*, f.farmer_name 
FROM farmer_crops_trade fct
JOIN farmerlogin f ON fct.farmer_fkid = f.farmer_id
WHERE fct.Crop_quantity > 1000000 OR fct.Crop_quantity < 0;

-- 3. Check for orphaned cart entries
SELECT c.* 
FROM cart c
LEFT JOIN crops cr ON c.crop_id = cr.Crop_id
LEFT JOIN farmerlogin f ON c.farmer_id = f.farmer_id
WHERE cr.Crop_id IS NULL OR f.farmer_id IS NULL;
