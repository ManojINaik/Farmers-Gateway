<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/function.php");

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_login_user'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Please log in to like or dislike videos'
    ]);
    exit();
}

// Validate input
if (!isset($_POST['video_id']) || !isset($_POST['action'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request parameters'
    ]);
    exit();
}

$video_id = intval($_POST['video_id']);
$action = $_POST['action'];
$user_id = $_SESSION['customer_login_user'];
$user_type = 'customer';

// Validate action
if (!in_array($action, ['like', 'dislike'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid action'
    ]);
    exit();
}

// Start transaction
mysqli_begin_transaction($connection);

try {
    // Check if user has already reacted to this video
    $check_query = "SELECT liked FROM farmtube_likes 
                    WHERE videoId = ? AND userId = ? AND userType = ?";
    $check_stmt = mysqli_prepare($connection, $check_query);
    mysqli_stmt_bind_param($check_stmt, "iss", $video_id, $user_id, $user_type);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $existing_reaction = mysqli_fetch_assoc($check_result);

    // Determine like/dislike status
    $is_like = ($action === 'like') ? 1 : 0;
    
    if ($existing_reaction) {
        // User has already reacted, update the reaction
        $update_query = "UPDATE farmtube_likes 
                         SET liked = ? 
                         WHERE videoId = ? AND userId = ? AND userType = ?";
        $update_stmt = mysqli_prepare($connection, $update_query);
        mysqli_stmt_bind_param($update_stmt, "iiss", $is_like, $video_id, $user_id, $user_type);
        mysqli_stmt_execute($update_stmt);
    } else {
        // First time reaction
        $insert_query = "INSERT INTO farmtube_likes 
                         (videoId, userId, userType, liked) 
                         VALUES (?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($connection, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "issi", $video_id, $user_id, $user_type, $is_like);
        mysqli_stmt_execute($insert_stmt);
    }

    // Update video likes/dislikes count
    $update_video_query = "UPDATE farmtube_videos 
                           SET likes = (
                               SELECT COUNT(*) FROM farmtube_likes 
                               WHERE videoId = ? AND liked = 1
                           ),
                           dislikes = (
                               SELECT COUNT(*) FROM farmtube_likes 
                               WHERE videoId = ? AND liked = 0
                           )
                           WHERE id = ?";
    $update_video_stmt = mysqli_prepare($connection, $update_video_query);
    mysqli_stmt_bind_param($update_video_stmt, "iii", $video_id, $video_id, $video_id);
    mysqli_stmt_execute($update_video_stmt);

    // Get updated like/dislike counts
    $count_query = "SELECT likes, dislikes FROM farmtube_videos WHERE id = ?";
    $count_stmt = mysqli_prepare($connection, $count_query);
    mysqli_stmt_bind_param($count_stmt, "i", $video_id);
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $counts = mysqli_fetch_assoc($count_result);

    // Commit transaction
    mysqli_commit($connection);

    // Return success response
    echo json_encode([
        'success' => true,
        'likes' => number_format($counts['likes']),
        'dislikes' => number_format($counts['dislikes']),
        'userAction' => $action
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($connection);

    // Log error and return error response
    error_log("Like/Dislike Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while processing your reaction'
    ]);
}

mysqli_close($connection);
exit();
?>
