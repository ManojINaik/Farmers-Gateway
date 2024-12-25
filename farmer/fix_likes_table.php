<?php
require_once("../includes/db.php");

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to log messages
function log_message($message) {
    echo $message . "<br>";
}

try {
    // Start transaction
    mysqli_begin_transaction($connection);

    // Drop existing farmtube_likes table
    $drop_table = "DROP TABLE IF EXISTS farmtube_likes";
    if (!mysqli_query($connection, $drop_table)) {
        throw new Exception("Error dropping table: " . mysqli_error($connection));
    }
    log_message("Dropped existing farmtube_likes table");

    // Create farmtube_likes table with proper constraints
    $create_table = "CREATE TABLE farmtube_likes (
        id INT(11) NOT NULL AUTO_INCREMENT,
        videoId INT(11) NOT NULL,
        userId VARCHAR(255) NOT NULL,
        userType ENUM('farmer', 'customer', 'admin') NOT NULL,
        liked TINYINT(1) NOT NULL DEFAULT 0,
        dateCreated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY unique_like (videoId, userId, userType),
        FOREIGN KEY (videoId) REFERENCES farmtube_videos(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if (!mysqli_query($connection, $create_table)) {
        throw new Exception("Error creating table: " . mysqli_error($connection));
    }
    log_message("Created farmtube_likes table with proper constraints");

    // Reset video like counts
    $reset_counts = "UPDATE farmtube_videos SET likes = 0, dislikes = 0";
    if (!mysqli_query($connection, $reset_counts)) {
        throw new Exception("Error resetting like counts: " . mysqli_error($connection));
    }
    log_message("Reset all video like/dislike counts");

    // Commit transaction
    mysqli_commit($connection);
    log_message("Successfully fixed farmtube_likes table structure!");

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($connection);
    log_message("Error: " . $e->getMessage());
}

// Close connection
mysqli_close($connection);
?>
