<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/function.php");

// Set default timezone
date_default_timezone_set('Asia/Kolkata');

// Check if user is logged in and determine user type
$user_type = '';
$user_id = '';

if(isset($_SESSION['farmer_login_user'])) {
    $user_type = 'farmer';
    $user_id = $_SESSION['farmer_login_user'];
} elseif(isset($_SESSION['customer_login_user'])) {
    $user_type = 'customer';
    $user_id = $_SESSION['customer_login_user'];
} elseif(isset($_SESSION['admin_login_user'])) {
    $user_type = 'admin';
    $user_id = $_SESSION['admin_login_user'];
} else {
    $_SESSION['ErrorMessage'] = "You must be logged in to comment.";
    header("Location: farmtube.php");
    exit();
}

// Check if form was submitted
if(!isset($_POST['video_id']) || !isset($_POST['comment'])) {
    $_SESSION['ErrorMessage'] = "Invalid form submission.";
    header("Location: farmtube.php");
    exit();
}

// Get and sanitize input
$video_id = mysqli_real_escape_string($connection, $_POST['video_id']);
$comment_text = mysqli_real_escape_string($connection, $_POST['comment']);

// Validate input
if(empty($comment_text)) {
    $_SESSION['ErrorMessage'] = "Comment cannot be empty.";
    header("Location: farmtube_watch.php?id=" . $video_id);
    exit();
}

// Get current timestamp in correct timezone
$current_time = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
$timestamp = $current_time->format('Y-m-d H:i:s');

// Insert comment using prepared statement
$query = "INSERT INTO farmtube_comments (postedBy, userType, videoId, body, datePosted) 
          VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($connection, $query);
if($stmt) {
    mysqli_stmt_bind_param($stmt, "ssiss", $user_id, $user_type, $video_id, $comment_text, $timestamp);
    
    if(mysqli_stmt_execute($stmt)) {
        $_SESSION['SuccessMessage'] = "Comment posted successfully!";
    } else {
        $_SESSION['ErrorMessage'] = "Error posting comment. Please try again.";
        error_log("Comment insert error: " . mysqli_error($connection));
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['ErrorMessage'] = "Database error. Please try again.";
    error_log("Comment prepare error: " . mysqli_error($connection));
}

// Always redirect back to the video page
header("Location: farmtube_watch.php?id=" . $video_id);
exit();
?>
