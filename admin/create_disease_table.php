<?php
require('../includes/db.php');

$sql = "CREATE TABLE IF NOT EXISTS plant_disease_detection (
    id INT AUTO_INCREMENT PRIMARY KEY,
    farmer_email VARCHAR(255) NOT NULL,
    disease_name VARCHAR(255),
    description TEXT,
    confidence_score FLOAT,
    image_path VARCHAR(255),
    detection_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX farmer_email_idx (farmer_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if (mysqli_query($connection, $sql)) {
    echo "Plant disease detection table created successfully";
} else {
    echo "Error creating table: " . mysqli_error($connection);
}
?>
