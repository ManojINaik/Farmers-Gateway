<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/function.php");

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION['customer_login_user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['customer_login_user'];

// Get and validate video ID
$video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;
if($video_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid video ID']);
    exit();
}

// Get and validate comment text
$comment_text = isset($_POST['comment']) ? trim($_POST['comment']) : '';
if(empty($comment_text)) {
    http_response_code(400);
    echo json_encode(['error' => 'Comment cannot be empty']);
    exit();
}

try {
    // Check if video exists
    $video_check = mysqli_prepare($connection, "SELECT id FROM farmtube_videos WHERE id = ?");
    if(!$video_check) {
        throw new Exception('Database error while checking video: ' . mysqli_error($connection));
    }
    
    mysqli_stmt_bind_param($video_check, "i", $video_id);
    if(!mysqli_stmt_execute($video_check)) {
        throw new Exception('Error executing video check: ' . mysqli_error($connection));
    }
    
    $video_result = mysqli_stmt_get_result($video_check);
    if(!$video_result) {
        throw new Exception('Error getting video result: ' . mysqli_error($connection));
    }

    if(mysqli_num_rows($video_result) == 0) {
        throw new Exception('Video not found');
    }

    // Begin transaction
    mysqli_begin_transaction($connection);

    try {
        // Get current timestamp in IST
        $current_time = date('Y-m-d H:i:s');
        
        // Insert comment
        $insert_stmt = mysqli_prepare($connection, 
            "INSERT INTO farmtube_comments (videoId, postedBy, userType, body, datePosted) 
             VALUES (?, ?, 'customer', ?, ?)"
        );
        
        if(!$insert_stmt) {
            throw new Exception('Database error while preparing comment: ' . mysqli_error($connection));
        }

        mysqli_stmt_bind_param($insert_stmt, "isss", $video_id, $user_id, $comment_text, $current_time);

        if(!mysqli_stmt_execute($insert_stmt)) {
            throw new Exception('Failed to post comment: ' . mysqli_error($connection));
        }

        $comment_id = mysqli_insert_id($connection);
        
        // Get the new comment's data
        $comment_query = "SELECT c.*, cl.cust_name as commenter_name, c.datePosted 
                         FROM farmtube_comments c 
                         LEFT JOIN custlogin cl ON c.postedBy = cl.email 
                         WHERE c.id = ?";
                         
        $comment_stmt = mysqli_prepare($connection, $comment_query);
        if(!$comment_stmt) {
            throw new Exception('Database error while fetching comment: ' . mysqli_error($connection));
        }
        
        mysqli_stmt_bind_param($comment_stmt, "i", $comment_id);
        if(!mysqli_stmt_execute($comment_stmt)) {
            throw new Exception('Error fetching comment data: ' . mysqli_error($connection));
        }
        
        $comment_result = mysqli_stmt_get_result($comment_stmt);
        if(!$comment_result) {
            throw new Exception('Error getting comment result: ' . mysqli_error($connection));
        }
        
        $comment_data = mysqli_fetch_assoc($comment_result);
        if(!$comment_data) {
            throw new Exception('Error retrieving comment data');
        }

        // Commit transaction
        mysqli_commit($connection);
        
        // Format the time for display
        $comment_date = new DateTime($comment_data['datePosted'], new DateTimeZone('Asia/Kolkata'));
        $formatted_date = $comment_date->format('Y-m-d H:i:s');
        
        // Send success response
        echo json_encode([
            'success' => true,
            'message' => 'Comment posted successfully',
            'comment' => [
                'id' => $comment_data['id'],
                'body' => $comment_data['body'],
                'commenter_name' => $comment_data['commenter_name'],
                'date_posted' => $formatted_date,
                'time_ago' => 'just now'
            ]
        ]);

    } catch (Exception $e) {
        mysqli_rollback($connection);
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'details' => [
            'video_id' => $video_id,
            'user_id' => $user_id
        ]
    ]);
} finally {
    // Clean up
    if(isset($video_check)) mysqli_stmt_close($video_check);
    if(isset($insert_stmt)) mysqli_stmt_close($insert_stmt);
    if(isset($comment_stmt)) mysqli_stmt_close($comment_stmt);
    mysqli_close($connection);
}
