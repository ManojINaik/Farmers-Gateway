<?php
require_once("../includes/db.php");

// First, check if tables exist
$tables_check = mysqli_query($connection, "
    SELECT TABLE_NAME 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = 'agriculture_portal' 
    AND TABLE_NAME IN ('farmtube_videos', 'farmtube_likes', 'farmtube_comments')
");

$existing_tables = [];
while ($row = mysqli_fetch_assoc($tables_check)) {
    $existing_tables[] = $row['TABLE_NAME'];
}

echo "Existing tables: " . implode(", ", $existing_tables) . "<br><br>";

// Drop existing tables if they exist (in reverse order due to foreign keys)
if (in_array('farmtube_likes', $existing_tables)) {
    mysqli_query($connection, "DROP TABLE IF EXISTS farmtube_likes");
    echo "Dropped farmtube_likes table<br>";
}
if (in_array('farmtube_comments', $existing_tables)) {
    mysqli_query($connection, "DROP TABLE IF EXISTS farmtube_comments");
    echo "Dropped farmtube_comments table<br>";
}
if (in_array('farmtube_videos', $existing_tables)) {
    mysqli_query($connection, "DROP TABLE IF EXISTS farmtube_videos");
    echo "Dropped farmtube_videos table<br>";
}

// Create tables
$create_videos = "
CREATE TABLE farmtube_videos (
    id INT NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    filePath VARCHAR(255) NOT NULL,
    thumbnail VARCHAR(255) NOT NULL,
    uploadedBy VARCHAR(255) NOT NULL,
    userType ENUM('farmer', 'customer', 'admin') NOT NULL,
    category VARCHAR(100) NOT NULL,
    privacy TINYINT(1) NOT NULL DEFAULT 1,
    views INT NOT NULL DEFAULT 0,
    likes INT NOT NULL DEFAULT 0,
    dislikes INT NOT NULL DEFAULT 0,
    uploadDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$create_likes = "
CREATE TABLE farmtube_likes (
    id INT NOT NULL AUTO_INCREMENT,
    videoId INT NOT NULL,
    postedBy VARCHAR(255) NOT NULL,
    userType ENUM('farmer', 'customer', 'admin') NOT NULL,
    liked TINYINT(1) NOT NULL,
    datePosted DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_like (videoId, postedBy, userType),
    FOREIGN KEY (videoId) REFERENCES farmtube_videos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$create_comments = "
CREATE TABLE farmtube_comments (
    id INT NOT NULL AUTO_INCREMENT,
    videoId INT NOT NULL,
    postedBy VARCHAR(255) NOT NULL,
    userType ENUM('farmer', 'customer', 'admin') NOT NULL,
    body TEXT NOT NULL,
    datePosted DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (videoId) REFERENCES farmtube_videos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Execute create table queries
$success = true;
$errors = [];

if (!mysqli_query($connection, $create_videos)) {
    $success = false;
    $errors[] = "Error creating videos table: " . mysqli_error($connection);
} else {
    echo "Created farmtube_videos table<br>";
}

if (!mysqli_query($connection, $create_likes)) {
    $success = false;
    $errors[] = "Error creating likes table: " . mysqli_error($connection);
} else {
    echo "Created farmtube_likes table<br>";
}

if (!mysqli_query($connection, $create_comments)) {
    $success = false;
    $errors[] = "Error creating comments table: " . mysqli_error($connection);
} else {
    echo "Created farmtube_comments table<br>";
}

// Add sample video if none exists
$video_check = mysqli_query($connection, "SELECT COUNT(*) as count FROM farmtube_videos");
$video_count = mysqli_fetch_assoc($video_check)['count'];

if ($video_count == 0) {
    $insert_video = mysqli_query($connection, "
        INSERT INTO farmtube_videos (
            title, description, filePath, thumbnail, uploadedBy, userType, category
        ) VALUES (
            'Sample Video',
            'This is a sample video',
            'sample.mp4',
            'sample.jpg',
            'agricultureportal01@gmail.com',
            'farmer',
            'General'
        )
    ");
    if ($insert_video) {
        echo "Added sample video<br>";
    } else {
        echo "Error adding sample video: " . mysqli_error($connection) . "<br>";
    }
}

echo "<br>Status: " . ($success ? "Success" : "Failed") . "<br>";
if (!empty($errors)) {
    echo "Errors:<br>";
    foreach ($errors as $error) {
        echo "- $error<br>";
    }
}
?>
