-- Drop tables if they exist
DROP TABLE IF EXISTS farmer_crops;
DROP TABLE IF EXISTS farmer_history;

-- Create farmer_crops table
CREATE TABLE farmer_crops (
    id INT PRIMARY KEY AUTO_INCREMENT,
    crop_id VARCHAR(20) NOT NULL UNIQUE,
    farmer_id INT NOT NULL,
    crop_name VARCHAR(255) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    price_per_kg DECIMAL(10,2) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    status ENUM('available', 'sold_out') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_farmercrop_farmer FOREIGN KEY (farmer_id) 
    REFERENCES farmerlogin(farmer_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create farmer_history table
CREATE TABLE farmer_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    farmer_id INT NOT NULL,
    farmer_crop VARCHAR(255) NOT NULL,
    farmer_quantity DECIMAL(10,2) NOT NULL,
    farmer_price DECIMAL(10,2) NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_farmerhistory_farmer FOREIGN KEY (farmer_id)
    REFERENCES farmerlogin(farmer_id) ON DELETE CASCADE
) ENGINE=InnoDB;
