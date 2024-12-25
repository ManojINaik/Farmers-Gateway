<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/function.php");

header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION['customer_login_user'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to like/dislike videos']);
    exit();
}

$user_id = $_SESSION['customer_login_user'];
$user_type = 'customer';

// Validate input
if(!isset($_POST['videoId']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$video_id = intval($_POST['videoId']);
$action = $_POST['action']; // 'like' or 'dislike'

if(!in_array($action, ['like', 'dislike'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

// First, check if the video exists
$video_check = mysqli_prepare($connection, "SELECT id FROM farmtube_videos WHERE id = ?");
if($video_check === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($connection)]);
    exit();
}

mysqli_stmt_bind_param($video_check, "i", $video_id);
mysqli_stmt_execute($video_check);
$video_result = mysqli_stmt_get_result($video_check);

if(mysqli_num_rows($video_result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Video not found']);
    exit();
}

// Begin transaction
mysqli_begin_transaction($connection);

try {
    // Check if user has already liked/disliked
    $check_stmt = mysqli_prepare($connection, 
        "SELECT * FROM farmtube_likes WHERE userId = ? AND userType = ? AND videoId = ? FOR UPDATE"
    );
    if($check_stmt === false) {
        throw new Exception('Database error: ' . mysqli_error($connection));
    }
    
    mysqli_stmt_bind_param($check_stmt, "ssi", $user_id, $user_type, $video_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if(mysqli_num_rows($check_result) > 0) {
        // User has already liked/disliked
        $existing = mysqli_fetch_assoc($check_result);
        
        if(($action === 'like' && $existing['liked'] == 1) || 
           ($action === 'dislike' && $existing['liked'] == 0)) {
            // Remove like/dislike if clicking same button
            $delete_stmt = mysqli_prepare($connection, 
                "DELETE FROM farmtube_likes WHERE id = ?"
            );
            if($delete_stmt === false) {
                throw new Exception('Database error: ' . mysqli_error($connection));
            }
            
            mysqli_stmt_bind_param($delete_stmt, "i", $existing['id']);
            mysqli_stmt_execute($delete_stmt);

            // Update video counts
            $update_video = mysqli_prepare($connection,
                "UPDATE farmtube_videos SET 
                likes = GREATEST(0, likes - ?), 
                dislikes = GREATEST(0, dislikes - ?)
                WHERE id = ?"
            );
            if($update_video === false) {
                throw new Exception('Database error: ' . mysqli_error($connection));
            }
            
            $like_dec = ($action === 'like') ? 1 : 0;
            $dislike_dec = ($action === 'dislike') ? 1 : 0;
            mysqli_stmt_bind_param($update_video, "iii", $like_dec, $dislike_dec, $video_id);
            mysqli_stmt_execute($update_video);
            
            $final_action = null;
        } else {
            // Switch from like to dislike or vice versa
            $update_stmt = mysqli_prepare($connection,
                "UPDATE farmtube_likes SET liked = ? WHERE id = ?"
            );
            if($update_stmt === false) {
                throw new Exception('Database error: ' . mysqli_error($connection));
            }
            
            $new_like = ($action === 'like') ? 1 : 0;
            mysqli_stmt_bind_param($update_stmt, "ii", $new_like, $existing['id']);
            mysqli_stmt_execute($update_stmt);

            // Update video counts
            $update_video = mysqli_prepare($connection,
                "UPDATE farmtube_videos SET 
                likes = likes + ?,
                dislikes = dislikes + ?
                WHERE id = ?"
            );
            if($update_video === false) {
                throw new Exception('Database error: ' . mysqli_error($connection));
            }
            
            $like_change = ($action === 'like') ? 1 : -1;
            $dislike_change = ($action === 'like') ? -1 : 1;
            mysqli_stmt_bind_param($update_video, "iii", $like_change, $dislike_change, $video_id);
            mysqli_stmt_execute($update_video);
            
            $final_action = $action;
        }
    } else {
        // New like/dislike
        $insert_stmt = mysqli_prepare($connection,
            "INSERT INTO farmtube_likes (videoId, userId, userType, liked) VALUES (?, ?, ?, ?)"
        );
        if($insert_stmt === false) {
            throw new Exception('Database error: ' . mysqli_error($connection));
        }
        
        $is_like = ($action === 'like') ? 1 : 0;
        mysqli_stmt_bind_param($insert_stmt, "issi", $video_id, $user_id, $user_type, $is_like);
        mysqli_stmt_execute($insert_stmt);

        // Update video counts
        $update_video = mysqli_prepare($connection,
            "UPDATE farmtube_videos SET 
            likes = likes + ?,
            dislikes = dislikes + ?
            WHERE id = ?"
        );
        if($update_video === false) {
            throw new Exception('Database error: ' . mysqli_error($connection));
        }
        
        $like_inc = ($action === 'like') ? 1 : 0;
        $dislike_inc = ($action === 'dislike') ? 1 : 0;
        mysqli_stmt_bind_param($update_video, "iii", $like_inc, $dislike_inc, $video_id);
        mysqli_stmt_execute($update_video);
        
        $final_action = $action;
    }

    // Get updated counts
    $count_stmt = mysqli_prepare($connection, 
        "SELECT likes, dislikes FROM farmtube_videos WHERE id = ?"
    );
    if($count_stmt === false) {
        throw new Exception('Database error: ' . mysqli_error($connection));
    }
    
    mysqli_stmt_bind_param($count_stmt, "i", $video_id);
    mysqli_stmt_execute($count_stmt);
    $counts = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt));

    mysqli_commit($connection);

    echo json_encode([
        'success' => true,
        'likes' => intval($counts['likes']),
        'dislikes' => intval($counts['dislikes']),
        'userAction' => $final_action
    ]);

} catch (Exception $e) {
    mysqli_rollback($connection);
    error_log("Error in like/dislike: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} finally {
    mysqli_close($connection);
}
?>
