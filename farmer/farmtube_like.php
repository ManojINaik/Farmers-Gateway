<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/function.php");

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log function
function debug_log($message, $data = null) {
    $log = date('Y-m-d H:i:s') . " - " . $message;
    if ($data !== null) {
        $log .= " - Data: " . json_encode($data);
    }
    error_log($log);
}

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
    debug_log("User not logged in");
    echo json_encode(['success' => false, 'message' => 'Please log in to like/dislike videos']);
    exit();
}

debug_log("User info", ['type' => $user_type, 'id' => $user_id]);

// Validate input
if(!isset($_POST['videoId']) || !isset($_POST['action'])) {
    debug_log("Invalid request", $_POST);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$video_id = intval($_POST['videoId']);
$action = $_POST['action']; // 'like' or 'dislike'

if(!in_array($action, ['like', 'dislike'])) {
    debug_log("Invalid action", ['action' => $action]);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

debug_log("Processing request", ['video_id' => $video_id, 'action' => $action]);

// Begin transaction
mysqli_begin_transaction($connection);

try {
    // First, check if video exists
    $video_check = mysqli_prepare($connection, "SELECT id, likes, dislikes FROM farmtube_videos WHERE id = ?");
    mysqli_stmt_bind_param($video_check, "i", $video_id);
    mysqli_stmt_execute($video_check);
    $video_result = mysqli_stmt_get_result($video_check);

    if(!$video_result || mysqli_num_rows($video_result) == 0) {
        throw new Exception("Video not found");
    }

    $video_data = mysqli_fetch_assoc($video_result);
    $current_likes = intval($video_data['likes']);
    $current_dislikes = intval($video_data['dislikes']);

    debug_log("Current video stats", ['likes' => $current_likes, 'dislikes' => $current_dislikes]);

    // Check if user has already liked/disliked
    $check_stmt = mysqli_prepare($connection, 
        "SELECT * FROM farmtube_likes WHERE videoId = ? AND userId = ? AND userType = ?"
    );
    mysqli_stmt_bind_param($check_stmt, "iss", $video_id, $user_id, $user_type);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);

    if(mysqli_num_rows($result) > 0) {
        // User has already interacted with this video
        $existing = mysqli_fetch_assoc($result);
        $current_state = $existing['liked'];

        debug_log("Existing interaction found", ['current_state' => $current_state]);

        if(($action === 'like' && $current_state == 0) || ($action === 'dislike' && $current_state == 1)) {
            // Toggle the like state
            $new_state = ($action === 'like') ? 1 : 0;
            $update_query = "UPDATE farmtube_likes SET liked = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($connection, $update_query);
            mysqli_stmt_bind_param($update_stmt, "ii", $new_state, $existing['id']);
            
            if (!mysqli_stmt_execute($update_stmt)) {
                throw new Exception("Failed to update like state");
            }

            // Update video counts
            if($action === 'like') {
                $new_likes = $current_likes + 1;
                $new_dislikes = $current_dislikes - 1;
            } else {
                $new_likes = $current_likes - 1;
                $new_dislikes = $current_dislikes + 1;
            }

            $update_video_query = "UPDATE farmtube_videos SET likes = ?, dislikes = ? WHERE id = ?";
            $update_video_stmt = mysqli_prepare($connection, $update_video_query);
            mysqli_stmt_bind_param($update_video_stmt, "iii", $new_likes, $new_dislikes, $video_id);
            
            if (!mysqli_stmt_execute($update_video_stmt)) {
                throw new Exception("Failed to update video counts");
            }

            $current_likes = $new_likes;
            $current_dislikes = $new_dislikes;
            $current_action = $action;

            debug_log("Toggled like state", [
                'new_state' => $new_state,
                'new_likes' => $new_likes,
                'new_dislikes' => $new_dislikes
            ]);
        } else {
            // Remove the like/dislike
            $delete_query = "DELETE FROM farmtube_likes WHERE id = ?";
            $delete_stmt = mysqli_prepare($connection, $delete_query);
            mysqli_stmt_bind_param($delete_stmt, "i", $existing['id']);
            
            if (!mysqli_stmt_execute($delete_stmt)) {
                throw new Exception("Failed to remove like/dislike");
            }

            // Update video counts
            if($current_state == 1) {
                $new_likes = $current_likes - 1;
                $new_dislikes = $current_dislikes;
            } else {
                $new_likes = $current_likes;
                $new_dislikes = $current_dislikes - 1;
            }

            $update_video_query = "UPDATE farmtube_videos SET likes = ?, dislikes = ? WHERE id = ?";
            $update_video_stmt = mysqli_prepare($connection, $update_video_query);
            mysqli_stmt_bind_param($update_video_stmt, "iii", $new_likes, $new_dislikes, $video_id);
            
            if (!mysqli_stmt_execute($update_video_stmt)) {
                throw new Exception("Failed to update video counts");
            }

            $current_likes = $new_likes;
            $current_dislikes = $new_dislikes;
            $current_action = null;

            debug_log("Removed like/dislike", [
                'new_likes' => $new_likes,
                'new_dislikes' => $new_dislikes
            ]);
        }
    } else {
        // New like/dislike
        $liked = ($action === 'like') ? 1 : 0;
        
        // Insert new like/dislike
        $insert_query = "INSERT INTO farmtube_likes (videoId, userId, userType, liked) VALUES (?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($connection, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "issi", $video_id, $user_id, $user_type, $liked);
        
        if (!mysqli_stmt_execute($insert_stmt)) {
            throw new Exception("Failed to insert like/dislike");
        }

        // Update video counts
        if($action === 'like') {
            $new_likes = $current_likes + 1;
            $new_dislikes = $current_dislikes;
        } else {
            $new_likes = $current_likes;
            $new_dislikes = $current_dislikes + 1;
        }

        $update_video_query = "UPDATE farmtube_videos SET likes = ?, dislikes = ? WHERE id = ?";
        $update_video_stmt = mysqli_prepare($connection, $update_video_query);
        mysqli_stmt_bind_param($update_video_stmt, "iii", $new_likes, $new_dislikes, $video_id);
        
        if (!mysqli_stmt_execute($update_video_stmt)) {
            throw new Exception("Failed to update video counts");
        }

        $current_likes = $new_likes;
        $current_dislikes = $new_dislikes;
        $current_action = $action;

        debug_log("Added new like/dislike", [
            'action' => $action,
            'new_likes' => $new_likes,
            'new_dislikes' => $new_dislikes
        ]);
    }

    mysqli_commit($connection);

    debug_log("Transaction committed", [
        'likes' => $current_likes,
        'dislikes' => $current_dislikes,
        'action' => $current_action
    ]);

    echo json_encode([
        'success' => true,
        'likes' => $current_likes,
        'dislikes' => $current_dislikes,
        'userAction' => $current_action
    ]);

} catch (Exception $e) {
    mysqli_rollback($connection);
    debug_log("Error occurred", ['error' => $e->getMessage()]);
    echo json_encode([
        'success' => false,
        'message' => 'Error processing your request: ' . $e->getMessage()
    ]);
}

mysqli_close($connection);
?>
