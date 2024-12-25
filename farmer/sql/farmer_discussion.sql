-- Create farmer discussion tables
CREATE TABLE IF NOT EXISTS farmer_discussions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    farmer_email VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS discussion_replies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    discussion_id INT NOT NULL,
    farmer_email VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (discussion_id) REFERENCES farmer_discussions(id)
);

-- Add indexes for better performance
ALTER TABLE farmer_discussions ADD INDEX idx_farmer_email (farmer_email);
ALTER TABLE discussion_replies ADD INDEX idx_farmer_email (farmer_email);
