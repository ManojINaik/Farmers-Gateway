<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/function.php");

// Set default timezone to match your server's timezone
date_default_timezone_set('Asia/Kolkata');

// Function to convert timestamp to "time ago" format
function time_elapsed_string($datetime) {
    try {
        $now = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $ago = new DateTime($datetime, new DateTimeZone('Asia/Kolkata'));
        
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
        
        // For debugging
        error_log("Now: " . $now->format('Y-m-d H:i:s') . " Ago: " . $ago->format('Y-m-d H:i:s'));
        error_log("Diff: Years=" . $diff->y . " Months=" . $diff->m . " Days=" . $diff->d . " Hours=" . $diff->h . " Minutes=" . $diff->i . " Seconds=" . $diff->s);
        
        // Convert to total seconds for accurate comparison
        $total_seconds = ($diff->y * 365 * 24 * 60 * 60) +
                        ($diff->m * 30 * 24 * 60 * 60) +
                        ($diff->d * 24 * 60 * 60) +
                        ($diff->h * 60 * 60) +
                        ($diff->i * 60) +
                        $diff->s;
        
        // Very recent (less than 60 seconds)
        if ($total_seconds < 60) {
            return "just now";
        }
        
        // Less than 1 hour
        if ($total_seconds < 3600) {
            $minutes = floor($total_seconds / 60);
            return ($minutes == 1) ? "1 minute ago" : $minutes . " minutes ago";
        }
        
        // Less than 1 day
        if ($total_seconds < 86400) {
            $hours = floor($total_seconds / 3600);
            return ($hours == 1) ? "1 hour ago" : $hours . " hours ago";
        }
        
        // Less than 1 week
        if ($total_seconds < 604800) {
            $days = floor($total_seconds / 86400);
            return ($days == 1) ? "1 day ago" : $days . " days ago";
        }
        
        // Less than 1 month
        if ($total_seconds < 2592000) {
            $weeks = floor($total_seconds / 604800);
            return ($weeks == 1) ? "1 week ago" : $weeks . " weeks ago";
        }
        
        // Less than 1 year
        if ($total_seconds < 31536000) {
            $months = floor($total_seconds / 2592000);
            return ($months == 1) ? "1 month ago" : $months . " months ago";
        }
        
        // More than 1 year
        $years = floor($total_seconds / 31536000);
        return ($years == 1) ? "1 year ago" : $years . " years ago";
        
    } catch (Exception $e) {
        error_log("Error in time_elapsed_string: " . $e->getMessage());
        return "some time ago";
    }
}

// Check if user is logged in (farmer, customer, or admin)
$user_type = '';
$user_id = '';

if(isset($_SESSION['farmer_login_user'])) {
    $user_type = 'farmer';
    $user_id = $_SESSION['farmer_login_user'];
    include("fheader.php");
} elseif(isset($_SESSION['customer_login_user'])) {
    $user_type = 'customer';
    $user_id = $_SESSION['customer_login_user'];
    include("../customer/cheader.php");
} elseif(isset($_SESSION['admin_login_user'])) {
    $user_type = 'admin';
    $user_id = $_SESSION['admin_login_user'];
    include("../admin/header.php");
} else {
    header("Location: ../index.php");
    exit();
}

if(!isset($_GET['id'])) {
    redirect_to("farmtube.php");
}

$video_id = mysqli_real_escape_string($connection, $_GET['id']);

// Get video details with uploader information
$query = "SELECT v.*, 
          CASE 
            WHEN v.userType = 'farmer' THEN f.farmer_name
            WHEN v.userType = 'customer' THEN c.cust_name
            WHEN v.userType = 'admin' THEN a.admin_name
          END as uploader_name
          FROM farmtube_videos v 
          LEFT JOIN farmerlogin f ON v.uploadedBy = f.email AND v.userType = 'farmer'
          LEFT JOIN custlogin c ON v.uploadedBy = c.email AND v.userType = 'customer'
          LEFT JOIN admin a ON v.uploadedBy = a.admin_name AND v.userType = 'admin'
          WHERE v.id = ?";

$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $video_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(!$result || mysqli_num_rows($result) == 0) {
    redirect_to("farmtube.php");
}

$video = mysqli_fetch_assoc($result);

// Update view count
$update_views = "UPDATE farmtube_videos SET views = views + 1 WHERE id = ?";
$stmt = mysqli_prepare($connection, $update_views);
mysqli_stmt_bind_param($stmt, "i", $video_id);
mysqli_stmt_execute($stmt);

$page_title = $video['title'] . " - FarmTube";
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch Video - FarmTube</title>
    <link rel="icon" type="image/x-icon" href="../images/leaf.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<style>
/* Main Content Styles */
.main-content {
    padding: 2rem;
    min-height: calc(100vh - 60px);
    background: #0f0f0f;
    color: #fff;
}

/* Video Container */
.video-container {
    background: #1a1a1a;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 2rem;
}

/* Video Title */
.video-title {
    color: #fff;
    font-size: 1.5rem;
    margin: 1rem 0;
}

/* Video Meta Info */
.video-meta {
    color: #aaa;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

/* Action Buttons */
.action-buttons .btn {
    margin: 0 5px;
    transition: all 0.3s ease;
}

.action-buttons .btn.active {
    transform: scale(1.1);
}

.like-btn.active {
    color: #007bff;
}

.dislike-btn.active {
    color: #dc3545;
}

.likes-count, .dislikes-count {
    margin-left: 5px;
}

/* Video Description */
.video-description {
    background: #1a1a1a;
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
    color: #ddd;
}

/* Comments Section */
.comments-section {
    background: #1a1a1a;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 2rem;
}

.comments-section h4 {
    color: #fff;
    margin-bottom: 1rem;
}

.comment-form textarea {
    background: #2d2d2d;
    border: none;
    color: #fff;
    border-radius: 4px;
}

.comment-form button {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    margin-top: 0.5rem;
}

/* Related Videos */
.related-videos {
    background: #1a1a1a;
    padding: 1rem;
    border-radius: 8px;
}

.related-video-item {
    background: #2d2d2d;
    border-radius: 4px;
    margin-bottom: 1rem;
    padding: 0.5rem;
    transition: all 0.2s;
}

.related-video-item:hover {
    background: #383838;
}

.related-video-item img {
    border-radius: 4px;
}

.related-video-item .title {
    color: #fff;
    font-size: 0.9rem;
    margin: 0.5rem 0;
}

.related-video-item .meta {
    color: #aaa;
    font-size: 0.8rem;
}
</style>

<div class="main-content">
    <div class="row">
        <!-- Navigation -->
        <?php include("fnav.php"); ?>
        
        <!-- Video Player Section -->
        <div class="col-md-8">
            <div class="video-container">
                <video style="width: 100%;" controls autoplay>
                    <source src="../uploads/videos/<?php echo htmlspecialchars($video['filePath']); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>

            <h1 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h1>
            
            <div class="video-meta d-flex justify-content-between align-items-center">
                <div>
                    <?php echo number_format($video['views']); ?> views • 
                    <?php echo time_elapsed_string($video['uploadDate']); ?>
                </div>
                <div class="action-buttons">
                    <button type="button" class="btn btn-outline-primary like-btn" data-video="<?php echo $video_id; ?>" onclick="console.log('Like button clicked');">
                        <i class="fas fa-thumbs-up"></i> 
                        <span class="likes-count"><?php echo number_format($video['likes']); ?></span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary dislike-btn" data-video="<?php echo $video_id; ?>" onclick="console.log('Dislike button clicked');">
                        <i class="fas fa-thumbs-down"></i>
                        <span class="dislikes-count"><?php echo number_format($video['dislikes']); ?></span>
                    </button>
                </div>
            </div>

            <div class="video-description">
                <div class="uploader-info mb-2">
                    <strong>Uploaded by:</strong> <?php echo htmlspecialchars($video['uploader_name']); ?>
                </div>
                <p><?php echo nl2br(htmlspecialchars($video['description'])); ?></p>
            </div>

            <div class="comments-section">
                <h4>Comments</h4>
                <form action="farmtube_comment.php" method="POST" class="comment-form mb-4">
                    <input type="hidden" name="video_id" value="<?php echo $video_id; ?>">
                    <div class="form-group">
                        <textarea class="form-control" name="comment" rows="3" placeholder="Add a comment..." required></textarea>
                    </div>
                    <button type="submit" name="submit_comment" class="btn">Post Comment</button>
                </form>

                <div class="comments-list">
                    <?php
                    // Fetch and display comments
                    $comments_query = "SELECT c.*, 
                                     CASE 
                                        WHEN c.userType = 'farmer' THEN f.farmer_name
                                        WHEN c.userType = 'customer' THEN cu.cust_name
                                        WHEN c.userType = 'admin' THEN a.admin_name
                                     END as commenter_name,
                                     c.datePosted as formatted_date
                                     FROM farmtube_comments c
                                     LEFT JOIN farmerlogin f ON c.postedBy = f.email AND c.userType = 'farmer'
                                     LEFT JOIN custlogin cu ON c.postedBy = cu.email AND c.userType = 'customer'
                                     LEFT JOIN admin a ON c.postedBy = a.admin_name AND c.userType = 'admin'
                                     WHERE c.videoId = ?
                                     ORDER BY c.datePosted DESC";
                    
                    if ($connection->error) {
                        echo '<div class="alert alert-warning">Database error: ' . htmlspecialchars($connection->error) . '</div>';
                    }
                    
                    $stmt = mysqli_prepare($connection, $comments_query);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "i", $video_id);
                        if (mysqli_stmt_execute($stmt)) {
                            $comments_result = mysqli_stmt_get_result($stmt);
                            
                            if ($comments_result) {
                                if (mysqli_num_rows($comments_result) > 0) {
                                    while($comment = mysqli_fetch_assoc($comments_result)) {
                                        $time_ago = time_elapsed_string($comment['formatted_date']);
                                        echo '<div class="comment-item" style="background: #2d2d2d; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">';
                                        echo '<div class="comment-header" style="color: #aaa; font-size: 0.9rem; margin-bottom: 0.5rem;">';
                                        echo '<strong>' . htmlspecialchars($comment['commenter_name']) . '</strong> • ';
                                        echo '<span>' . $time_ago . '</span>';
                                        echo '</div>';
                                        echo '<div class="comment-content" style="color: #fff;">';
                                        echo nl2br(htmlspecialchars($comment['body']));
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<div class="alert alert-info">No comments yet. Be the first to comment!</div>';
                                }
                                mysqli_free_result($comments_result);
                            } else {
                                echo '<div class="alert alert-warning">Error retrieving comments: ' . htmlspecialchars(mysqli_error($connection)) . '</div>';
                            }
                        } else {
                            echo '<div class="alert alert-warning">Error executing comment query: ' . htmlspecialchars(mysqli_stmt_error($stmt)) . '</div>';
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        echo '<div class="alert alert-warning">Error preparing comment query: ' . htmlspecialchars(mysqli_error($connection)) . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Related Videos Section -->
        <div class="col-md-4">
            <div class="related-videos">
                <h4 class="mb-3">Related Videos</h4>
                <?php
                // Get related videos (same category, excluding current video)
                $related_query = "SELECT v.*, 
                                CASE 
                                    WHEN v.userType = 'farmer' THEN f.farmer_name
                                    WHEN v.userType = 'customer' THEN c.cust_name
                                    WHEN v.userType = 'admin' THEN a.admin_name
                                END as uploader_name
                                FROM farmtube_videos v 
                                LEFT JOIN farmerlogin f ON v.uploadedBy = f.email AND v.userType = 'farmer'
                                LEFT JOIN custlogin c ON v.uploadedBy = c.email AND v.userType = 'customer'
                                LEFT JOIN admin a ON v.uploadedBy = a.admin_name AND v.userType = 'admin'
                                WHERE v.category = ? AND v.id != ? AND v.privacy = 1
                                ORDER BY v.views DESC
                                LIMIT 5";
                
                $stmt = mysqli_prepare($connection, $related_query);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "si", $video['category'], $video_id);
                    mysqli_stmt_execute($stmt);
                    $related_result = mysqli_stmt_get_result($stmt);

                    while($related_video = mysqli_fetch_assoc($related_result)) {
                        echo '<a href="farmtube_watch.php?id=' . $related_video['id'] . '" class="related-video-item d-block text-decoration-none">';
                        echo '<div class="row no-gutters">';
                        echo '<div class="col-5">';
                        echo '<img src="../uploads/thumbnails/' . htmlspecialchars($related_video['thumbnail']) . '" class="img-fluid" alt="Thumbnail">';
                        echo '</div>';
                        echo '<div class="col-7 pl-2">';
                        echo '<div class="title">' . htmlspecialchars($related_video['title']) . '</div>';
                        echo '<div class="meta">';
                        echo htmlspecialchars($related_video['uploader_name']) . '<br>';
                        echo number_format($related_video['views']) . ' views • ' . time_elapsed_string($related_video['uploadDate']);
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</a>';
                    }
                    mysqli_stmt_close($stmt);
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include("../modern-footer.php"); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    
    // Initialize button states
    function updateButtonStates(action) {
        console.log('Updating button states:', action);
        const $likeBtn = $('.like-btn');
        const $dislikeBtn = $('.dislike-btn');
        
        // Reset both buttons
        $likeBtn.removeClass('active btn-primary').addClass('btn-outline-primary');
        $dislikeBtn.removeClass('active btn-secondary').addClass('btn-outline-secondary');
        
        // Set active state
        if(action === 'like') {
            $likeBtn.removeClass('btn-outline-primary').addClass('active btn-primary');
        } else if(action === 'dislike') {
            $dislikeBtn.removeClass('btn-outline-secondary').addClass('active btn-secondary');
        }
    }

    // Handle like/dislike buttons
    $('.like-btn, .dislike-btn').on('click', function(e) {
        e.preventDefault();
        console.log('Button clicked');
        
        <?php if(!isset($user_id) || empty($user_id)): ?>
            alert('Please log in to like or dislike videos');
            return;
        <?php endif; ?>
        
        const $btn = $(this);
        const videoId = $btn.data('video');
        const action = $btn.hasClass('like-btn') ? 'like' : 'dislike';
        
        console.log('Processing click:', {
            videoId: videoId,
            action: action,
            button: $btn.prop('outerHTML')
        });
        
        // Disable buttons during request
        $('.like-btn, .dislike-btn').prop('disabled', true);
        
        $.ajax({
            url: 'farmtube_like.php',
            type: 'POST',
            data: {
                videoId: videoId,
                action: action
            },
            dataType: 'json',
            success: function(response) {
                console.log('Server response:', response);
                
                if(response.success) {
                    // Update counts with animation
                    const $likesCount = $('.likes-count');
                    const $dislikesCount = $('.dislikes-count');
                    
                    console.log('Updating counts:', {
                        oldLikes: $likesCount.text(),
                        newLikes: response.likes,
                        oldDislikes: $dislikesCount.text(),
                        newDislikes: response.dislikes
                    });
                    
                    $likesCount.fadeOut(200, function() {
                        $(this).text(response.likes).fadeIn(200);
                    });
                    
                    $dislikesCount.fadeOut(200, function() {
                        $(this).text(response.dislikes).fadeIn(200);
                    });
                    
                    // Update button states
                    updateButtonStates(response.userAction);
                    
                    console.log('Updated button states:', {
                        action: response.userAction
                    });
                } else {
                    console.error('Error:', response.message);
                    alert(response.message || 'Error processing like/dislike');
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                alert('Error connecting to server. Please try again.');
            },
            complete: function() {
                // Re-enable buttons after a short delay
                setTimeout(function() {
                    $('.like-btn, .dislike-btn').prop('disabled', false);
                }, 500);
            }
        });
    });
    
    // Initialize button states on page load
    <?php
    if(isset($user_id) && !empty($user_id)) {
        $initial_state_query = "SELECT liked FROM farmtube_likes WHERE videoId = ? AND userId = ? AND userType = ? LIMIT 1";
        $stmt = mysqli_prepare($connection, $initial_state_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iss", $video_id, $user_id, $user_type);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if($row = mysqli_fetch_assoc($result)) {
                    echo "updateButtonStates('".($row['liked'] ? "like" : "dislike")."');";
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
    ?>
});
</script>
