<?php
require_once("../includes/db.php");

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add likes and dislikes columns if they don't exist
$alter_query = "ALTER TABLE farmtube_videos 
                ADD COLUMN IF NOT EXISTS likes INT DEFAULT 0,
                ADD COLUMN IF NOT EXISTS dislikes INT DEFAULT 0";

if(mysqli_query($connection, $alter_query)) {
    echo "Successfully added likes and dislikes columns\n";
} else {
    echo "Error adding columns: " . mysqli_error($connection) . "\n";
}

// Check if farmtube_likes table exists
$check_table = "SHOW TABLES LIKE 'farmtube_likes'";
$result = mysqli_query($connection, $check_table);

if(mysqli_num_rows($result) == 0) {
    // Create farmtube_likes table if it doesn't exist
    $create_table = "CREATE TABLE farmtube_likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        videoId INT NOT NULL,
        postedBy VARCHAR(255) NOT NULL,
        userType VARCHAR(50) NOT NULL,
        liked TINYINT NOT NULL DEFAULT 0,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_like (videoId, postedBy, userType)
    )";
    
    if(mysqli_query($connection, $create_table)) {
        echo "Successfully created farmtube_likes table\n";
    } else {
        echo "Error creating table: " . mysqli_error($connection) . "\n";
    }
}

// Update video counts
$update_counts = "UPDATE farmtube_videos v 
                  SET likes = (
                      SELECT COUNT(*) 
                      FROM farmtube_likes 
                      WHERE videoId = v.id AND liked = 1
                  ),
                  dislikes = (
                      SELECT COUNT(*) 
                      FROM farmtube_likes 
                      WHERE videoId = v.id AND liked = 0
                  )";

if(mysqli_query($connection, $update_counts)) {
    echo "Successfully updated video like/dislike counts\n";
} else {
    echo "Error updating counts: " . mysqli_error($connection) . "\n";
}

mysqli_close($connection);
?>
