-- Backup existing cart data
CREATE TEMPORARY TABLE cart_backup AS SELECT * FROM cart;

-- Drop existing cart table
DROP TABLE IF EXISTS cart;

-- Create new cart table with updated structure
CREATE TABLE cart (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `crop_id` int(11) NOT NULL,
    `farmer_id` int(11) NOT NULL,
    `quantity` decimal(10,2) NOT NULL,
    `price` decimal(10,2) NOT NULL,
    `cust_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `cust_id` (`cust_id`),
    KEY `farmer_id` (`farmer_id`),
    UNIQUE KEY `unique_crop_farmer_customer` (`crop_id`, `farmer_id`, `cust_id`),
    CONSTRAINT `cart_customer_fk` FOREIGN KEY (`cust_id`) REFERENCES `custlogin` (`cust_id`) ON DELETE CASCADE,
    CONSTRAINT `cart_farmer_fk` FOREIGN KEY (`farmer_id`) REFERENCES `farmerlogin` (`farmer_id`) ON DELETE CASCADE,
    CONSTRAINT `cart_crop_fk` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`Crop_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Restore cart data with proper relationships
INSERT INTO cart (crop_id, farmer_id, quantity, price, cust_id, created_at)
SELECT 
    c.Crop_id,
    fct.farmer_fkid,
    cb.quantity,
    cb.price,
    cb.cust_id,
    cb.created_at
FROM cart_backup cb
JOIN crops c ON cb.crop_id = c.Crop_id
JOIN farmer_crops_trade fct ON c.Crop_id = fct.trade_id;

-- Drop temporary table
DROP TEMPORARY TABLE cart_backup;
