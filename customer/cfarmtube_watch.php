<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/function.php");

if(!isset($_SESSION['customer_login_user'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['customer_login_user'];
$user_type = 'customer';

// Helper function for time ago format
function time_elapsed_string($datetime) {
    $timezone = new DateTimeZone('Asia/Kolkata');
    $now = new DateTime('now', $timezone);
    $ago = new DateTime($datetime, $timezone);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $periods = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second'
    );

    foreach ($periods as $key => $period) {
        if ($diff->$key) {
            $time = $diff->$key . ' ' . $period;
            return $diff->$key > 1 ? $time . 's ago' : $time . ' ago';
        }
    }

    return 'just now';
}

include("cheader.php");

// Get video ID and validate
if(!isset($_GET['v'])) {
    header("Location: cfarmtube.php");
    exit();
}

$video_id = intval($_GET['v']);

// Get video details with uploader information
$video_query = "SELECT v.*, 
    CASE 
        WHEN v.userType = 'farmer' THEN f.farmer_name
        WHEN v.userType = 'customer' THEN c.cust_name
        WHEN v.userType = 'admin' THEN a.admin_name
    END as uploader_name,
    COALESCE(l.liked, -1) as user_reaction
    FROM farmtube_videos v 
    LEFT JOIN farmerlogin f ON v.uploadedBy = f.email AND v.userType = 'farmer'
    LEFT JOIN custlogin c ON v.uploadedBy = c.email AND v.userType = 'customer'
    LEFT JOIN admin a ON v.uploadedBy = a.admin_name AND v.userType = 'admin'
    LEFT JOIN farmtube_likes l ON v.id = l.videoId 
        AND l.userId = ? AND l.userType = ?
    WHERE v.id = ? AND v.privacy = 1";

$stmt = mysqli_prepare($connection, $video_query);
if($stmt === false) {
    die("Error preparing statement: " . mysqli_error($connection));
}

mysqli_stmt_bind_param($stmt, "ssi", $user_id, $user_type, $video_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    header("Location: cfarmtube.php");
    exit();
}

$video = mysqli_fetch_assoc($result);

// Update view count
$update_views = mysqli_prepare($connection, "UPDATE farmtube_videos SET views = views + 1 WHERE id = ?");
mysqli_stmt_bind_param($update_views, "i", $video_id);
mysqli_stmt_execute($update_views);

// Get comments for this video
$comments_query = "SELECT c.*, 
    CASE 
        WHEN c.userType = 'farmer' THEN f.farmer_name
        WHEN c.userType = 'customer' THEN cl.cust_name
        WHEN c.userType = 'admin' THEN a.admin_name
    END as commenter_name,
    c.datePosted
    FROM farmtube_comments c
    LEFT JOIN farmerlogin f ON c.postedBy = f.email AND c.userType = 'farmer'
    LEFT JOIN custlogin cl ON c.postedBy = cl.email AND c.userType = 'customer'
    LEFT JOIN admin a ON c.postedBy = a.admin_name AND c.userType = 'admin'
    WHERE c.videoId = ?
    ORDER BY c.datePosted DESC";

$comments_stmt = mysqli_prepare($connection, $comments_query);
mysqli_stmt_bind_param($comments_stmt, "i", $video_id);
mysqli_stmt_execute($comments_stmt);
$comments_result = mysqli_stmt_get_result($comments_stmt);

$page_title = $video['title'] . " - FarmTube";
?>

<style>
/* Professional Dark UI Theme */
:root {
    --primary-color: #2ecc71;
    --primary-dark: #27ae60;
    --secondary-color: #3498db;
    --accent-color: #e74c3c;
    --bg-primary: #1a1a1a;
    --bg-secondary: #242424;
    --bg-accent: #2d2d2d;
    --text-primary: #ffffff;
    --text-secondary: #b3b3b3;
    --border-color: #333333;
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.2);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.25);
    --shadow-lg: 0 10px 15px rgba(0,0,0,0.3);
    --radius-sm: 4px;
    --radius-md: 8px;
    --radius-lg: 12px;
}

body {
    background-color: var(--bg-primary);
    color: var(--text-primary);
}

/* Related Videos Section */
.related-videos {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-top: 2rem;
    overflow: hidden;
}

.related-videos h3 {
    padding: 1.25rem;
    margin: 0;
    font-size: 1.25rem;
    color: var(--text-primary);
    border-bottom: 1px solid var(--border-color);
    background: var(--bg-accent);
    font-weight: 600;
}

.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
    background: var(--bg-secondary);
}

.video-card {
    background: var(--bg-accent);
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
}

.video-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-color);
    background: var(--bg-primary);
}

.video-link {
    text-decoration: none;
    color: var(--text-primary);
    display: block;
}

.video-link:hover {
    text-decoration: none;
    color: var(--text-primary);
}

.thumbnail {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    background: var(--bg-primary);
    overflow: hidden;
}

.thumbnail img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.video-card:hover .thumbnail img {
    transform: scale(1.05);
}

.video-info {
    padding: 1rem;
    background: var(--bg-accent);
}

.video-card:hover .video-info {
    background: var(--bg-primary);
}

.video-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.4;
    max-height: 2.8em;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.video-meta {
    margin-top: 0.75rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.video-meta i {
    font-size: 0.75rem;
    opacity: 0.7;
    color: var(--primary-color);
}

.video-stats {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid var(--border-color);
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.video-stats span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.video-stats i {
    font-size: 0.875rem;
    opacity: 0.8;
    color: var(--primary-color);
}

/* Back Button Dark Theme */
.back-button {
    background: var(--bg-accent);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
    padding: 0.75rem 1.25rem;
    border-radius: var(--radius-lg);
    transition: all 0.3s ease;
}

.back-button:hover {
    background: var(--bg-primary);
    color: var(--primary-color);
    transform: translateX(-3px);
    border-color: var(--primary-color);
}

.back-button i {
    color: var(--primary-color);
}

/* Main Content Area */
.container-fluid {
    background: var(--bg-primary);
    min-height: 100vh;
    padding-top: 2rem;
    padding-bottom: 2rem;
}

/* Video Player Section */
.video-container {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    padding: 1rem;
    margin-bottom: 2rem;
    border: 1px solid var(--border-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    .video-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1rem;
        padding: 1rem;
    }
    
    .container-fluid {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
}

/* Video Controls Dark Theme */
.plyr {
    --plyr-color-main: var(--primary-color);
    --plyr-video-background: var(--bg-primary);
    --plyr-menu-background: var(--bg-secondary);
    --plyr-menu-color: var(--text-primary);
    --plyr-tooltip-background: var(--bg-secondary);
    --plyr-tooltip-color: var(--text-primary);
}

/* Scrollbar Styling */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--bg-secondary);
}

::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-color);
}

/* Video Details Container */
.video-details-container {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-top: 1.5rem;
    border: 1px solid var(--border-color);
}

.video-info-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.video-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
    line-height: 1.4;
}

.video-meta-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.video-stats {
    display: flex;
    gap: 1.5rem;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.video-stats span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.video-stats i {
    color: var(--primary-color);
}

/* Interaction Buttons */
.interaction-buttons {
    display: flex;
    gap: 1rem;
}

.btn-like, .btn-dislike {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    background: var(--bg-accent);
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-like:hover, .btn-dislike:hover {
    background: var(--bg-primary);
    border-color: var(--primary-color);
}

.btn-like.active {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.btn-dislike.active {
    background: var(--accent-color);
    border-color: var(--accent-color);
    color: white;
}

/* Comments Section */
.comments-section {
    margin-top: 2rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
}

.comment-form-container {
    margin-bottom: 2rem;
}

.comment-form {
    background: var(--bg-accent);
    border-radius: var(--radius-md);
    padding: 1rem;
}

.comment-input {
    width: 100%;
    min-height: 100px;
    padding: 1rem;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    color: var(--text-primary);
    resize: vertical;
    transition: all 0.3s ease;
}

.comment-input:focus {
    outline: none;
    border-color: var(--primary-color);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1rem;
}

.btn-cancel, .btn-submit {
    padding: 0.5rem 1rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border-color);
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-cancel {
    background: transparent;
    color: var(--text-secondary);
}

.btn-submit {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.btn-cancel:hover {
    background: var(--bg-primary);
    color: var(--text-primary);
}

.btn-submit:hover {
    background: var(--primary-dark);
}

.login-prompt {
    text-align: center;
    padding: 2rem;
    background: var(--bg-accent);
    border-radius: var(--radius-md);
    color: var(--text-secondary);
}

.login-prompt i {
    margin-right: 0.5rem;
    color: var(--primary-color);
}

.login-prompt a {
    color: var(--primary-color);
    text-decoration: none;
}

.login-prompt a:hover {
    text-decoration: underline;
}

/* Comments Display */
.comments-container {
    margin-top: 2rem;
}

.comment {
    background: var(--bg-accent);
    border-radius: var(--radius-md);
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.comment:hover {
    background: var(--bg-primary);
    transform: translateX(4px);
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.comment-user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--bg-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
}

.comment-meta {
    display: flex;
    flex-direction: column;
}

.comment-author {
    font-weight: 600;
    color: var(--text-primary);
}

.comment-time {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.comment-content {
    color: var(--text-primary);
    line-height: 1.6;
    word-break: break-word;
}

/* Responsive Design */
@media (max-width: 768px) {
    .video-meta-info {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .interaction-buttons {
        width: 100%;
        justify-content: space-between;
    }
    
    .btn-like, .btn-dislike {
        flex: 1;
        justify-content: center;
    }
}

.alert {
    padding: 10px;
    border-radius: var(--radius-sm);
    margin-bottom: 1rem;
}

.alert-success {
    background-color: rgba(46, 204, 113, 0.1);
    border: 1px solid #2ecc71;
    color: #2ecc71;
}

.alert-error {
    background-color: rgba(231, 76, 60, 0.1);
    border: 1px solid #e74c3c;
    color: #e74c3c;
}

.no-comments {
    text-align: center;
    padding: 2rem;
    color: var(--text-secondary);
    background: var(--bg-accent);
    border-radius: var(--radius-md);
    margin-top: 1rem;
}
</style>

<div class="container-fluid py-4">
    <!-- Back Button -->
    <a href="cfarmtube.php" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to Videos
    </a>
    
    <div class="row">
        <!-- Video Player Column -->
        <div class="col-lg-8">
            <!-- Video Player -->
            <div class="video-container">
                <video controls autoplay class="w-100">
                    <source src="../uploads/videos/<?php echo htmlspecialchars($video['filePath']); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>

            <!-- Video Details and Comments Section -->
            <div class="video-details-container">
                <div class="video-info-section">
                    <h1 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h1>
                    <div class="video-meta-info">
                        <div class="video-stats">
                            <span><i class="fas fa-eye"></i> <?php echo number_format($video['views']); ?> views</span>
                            <span><i class="fas fa-calendar"></i> <?php echo date("M j, Y", strtotime($video['uploadDate'])); ?></span>
                        </div>
                        <div class="interaction-buttons">
                            <button class="btn-like" data-video="<?php echo $video_id; ?>" <?php echo !isset($_SESSION['customer_login_user']) ? 'disabled' : ''; ?>>
                                <i class="fas fa-thumbs-up"></i>
                                <span class="likes-count"><?php echo number_format($video['likes']); ?></span>
                            </button>
                            <button class="btn-dislike" data-video="<?php echo $video_id; ?>" <?php echo !isset($_SESSION['customer_login_user']) ? 'disabled' : ''; ?>>
                                <i class="fas fa-thumbs-down"></i>
                                <span class="dislikes-count"><?php echo number_format($video['dislikes']); ?></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="comments-section">
                    <h3 class="section-title">Comments</h3>
                    
                    <?php if(isset($_SESSION['customer_login_user'])): ?>
                    <div class="comment-form-container">
                        <div id="comment-status" class="alert" style="display: none;"></div>
                        <form id="commentForm" class="comment-form" onsubmit="return false;">
                            <input type="hidden" name="video_id" value="<?php echo $video_id; ?>">
                            <div class="form-group">
                                <textarea 
                                    class="comment-input" 
                                    name="comment" 
                                    placeholder="Add a comment..."
                                    required
                                ></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn-cancel">Cancel</button>
                                <button type="button" class="btn-submit" onclick="submitComment()">Comment</button>
                            </div>
                        </form>
                    </div>
                    <?php else: ?>
                    <div class="login-prompt">
                        <p><i class="fas fa-lock"></i> Please <a href="../login.php">login</a> to comment</p>
                    </div>
                    <?php endif; ?>

                    <div class="comments-container">
                        <?php
                        // Debug information
                        error_reporting(E_ALL);
                        ini_set('display_errors', 1);

                        // Fetch comments with user information
                        $comments_query = "SELECT c.*, 
                            CASE 
                                WHEN c.userType = 'farmer' THEN f.farmer_name
                                WHEN c.userType = 'customer' THEN cu.cust_name
                                ELSE 'Unknown'
                            END as commenter_name,
                            c.datePosted
                            FROM farmtube_comments c
                            LEFT JOIN farmerlogin f ON c.postedBy = f.email AND c.userType = 'farmer'
                            LEFT JOIN custlogin cu ON c.postedBy = cu.email AND c.userType = 'customer'
                            WHERE c.videoId = ?
                            ORDER BY c.datePosted DESC";

                        try {
                            if($stmt = mysqli_prepare($connection, $comments_query)) {
                                mysqli_stmt_bind_param($stmt, "i", $video_id);
                                
                                if(!mysqli_stmt_execute($stmt)) {
                                    throw new Exception("Error executing query: " . mysqli_error($connection));
                                }
                                
                                $comments_result = mysqli_stmt_get_result($stmt);
                                
                                if($comments_result === false) {
                                    throw new Exception("Error getting result: " . mysqli_error($connection));
                                }

                                if(mysqli_num_rows($comments_result) > 0) {
                                    while($comment = mysqli_fetch_assoc($comments_result)) {
                                        $time_ago = time_elapsed_string($comment['datePosted']);
                                        ?>
                                        <div class="comment">
                                            <div class="comment-header">
                                                <div class="comment-user-avatar">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div class="comment-meta">
                                                    <span class="comment-author"><?php echo htmlspecialchars($comment['commenter_name']); ?></span>
                                                    <span class="comment-time" title="<?php echo date('Y-m-d H:i:s', strtotime($comment['datePosted'])); ?>">
                                                        <?php echo $time_ago; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="comment-content">
                                                <?php echo nl2br(htmlspecialchars($comment['body'])); ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo '<div class="no-comments">No comments yet. Be the first to comment!</div>';
                                }
                                mysqli_stmt_close($stmt);
                            } else {
                                throw new Exception("Error preparing statement: " . mysqli_error($connection));
                            }
                        } catch (Exception $e) {
                            echo '<div class="alert alert-error">Error loading comments: ' . htmlspecialchars($e->getMessage()) . '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Videos Column -->
        <div class="col-lg-4">
            <div class="related-videos">
                <h3>Related Videos</h3>
                <div class="video-grid">
                    <?php
                    // Get related videos
                    $related_query = "SELECT 
                        v.*,
                        CASE 
                            WHEN v.userType = 'farmer' THEN f.farmer_name
                            WHEN v.userType = 'customer' THEN c.cust_name
                            WHEN v.userType = 'admin' THEN a.admin_name
                            ELSE 'Unknown'
                        END as uploader_name
                        FROM farmtube_videos v
                        LEFT JOIN farmerlogin f ON v.uploadedBy = f.email AND v.userType = 'farmer'
                        LEFT JOIN custlogin c ON v.uploadedBy = c.email AND v.userType = 'customer'
                        LEFT JOIN admin a ON v.uploadedBy = a.admin_name AND v.userType = 'admin'
                        WHERE v.id != ? AND v.privacy = 1
                        ORDER BY v.uploadDate DESC 
                        LIMIT 6";
                        
                    $stmt = mysqli_prepare($connection, $related_query);
                    if ($stmt === false) {
                        error_log("Failed to prepare related videos query: " . mysqli_error($connection));
                    } else {
                        mysqli_stmt_bind_param($stmt, "i", $video_id);
                        if (!mysqli_stmt_execute($stmt)) {
                            error_log("Failed to execute related videos query: " . mysqli_stmt_error($stmt));
                        } else {
                            $related_result = mysqli_stmt_get_result($stmt);
                            
                            if ($related_result === false) {
                                error_log("Failed to get result set: " . mysqli_error($connection));
                            } else {
                                while($related_video = mysqli_fetch_assoc($related_result)) {
                                    // Get video thumbnail or use default
                                    $thumbnail = !empty($related_video['thumbnail']) ? 
                                        "../uploads/thumbnails/" . $related_video['thumbnail'] : 
                                        "../assets/img/default-thumbnail.jpg";
                                    
                                    // Get video preview image from video if no thumbnail
                                    if (!file_exists($thumbnail)) {
                                        $thumbnail = "../uploads/videos/" . pathinfo($related_video['filePath'], PATHINFO_FILENAME) . ".jpg";
                                        if (!file_exists($thumbnail)) {
                                            $thumbnail = "../assets/img/default-thumbnail.jpg";
                                        }
                                    }
                                    // Get video age
                                    $video_date = new DateTime($related_video['uploadDate']);
                                    $now = new DateTime();
                                    $interval = $now->diff($video_date);
                                    
                                    if ($interval->y > 0) {
                                        $time_ago = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
                                    } elseif ($interval->m > 0) {
                                        $time_ago = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
                                    } elseif ($interval->d > 0) {
                                        $time_ago = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
                                    } elseif ($interval->h > 0) {
                                        $time_ago = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
                                    } else {
                                        $time_ago = 'Just now';
                                    }
                                    ?>
                                    <div class="video-card">
                                        <a href="cfarmtube_watch.php?v=<?php echo $related_video['id']; ?>" class="video-link">
                                            <div class="thumbnail">
                                                <img src="<?php echo htmlspecialchars($thumbnail); ?>" alt="<?php echo htmlspecialchars($related_video['title']); ?> thumbnail">
                                            </div>
                                            <div class="video-info">
                                                <h4 class="video-title"><?php echo htmlspecialchars($related_video['title']); ?></h4>
                                                <div class="video-meta">
                                                    <i class="fas fa-user"></i>
                                                    <?php echo htmlspecialchars($related_video['uploader_name']); ?>
                                                </div>
                                                <div class="video-stats">
                                                    <span>
                                                        <i class="fas fa-eye"></i>
                                                        <?php echo number_format($related_video['views']); ?>
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-clock"></i>
                                                        <?php echo $time_ago; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        mysqli_stmt_close($stmt);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    
    // Initialize button states
    function updateButtonStates(action) {
        console.log('Updating button states:', action);
        const $likeBtn = $('.btn-like');
        const $dislikeBtn = $('.btn-dislike');
        
        // Reset both buttons
        $likeBtn.removeClass('active');
        $dislikeBtn.removeClass('active');
        
        // Set active state
        if(action === 'like') {
            $likeBtn.addClass('active');
        } else if(action === 'dislike') {
            $dislikeBtn.addClass('active');
        }
    }

    // Handle like/dislike buttons
    $('.btn-like, .btn-dislike').on('click', function(e) {
        e.preventDefault();
        console.log('Button clicked');
        
        <?php if(!isset($_SESSION['customer_login_user'])): ?>
            alert('Please log in to like or dislike videos');
            return;
        <?php endif; ?>
        
        const $btn = $(this);
        const videoId = $btn.data('video');
        const action = $btn.hasClass('btn-like') ? 'like' : 'dislike';
        
        console.log('Processing click:', {
            videoId: videoId,
            action: action,
            button: $btn.prop('outerHTML')
        });
        
        // Disable buttons during request
        $('.btn-like, .btn-dislike').prop('disabled', true);
        
        $.ajax({
            url: 'cfarmtube_react.php',
            type: 'POST',
            data: {
                video_id: videoId,
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
                    updateButtonStates(action);
                    
                    console.log('Updated button states:', {
                        action: action
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
                    $('.btn-like, .btn-dislike').prop('disabled', false);
                }, 500);
            }
        });
    });
    
    // Initialize button states on page load
    <?php
    if(isset($_SESSION['customer_login_user'])) {
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

function submitComment() {
    const $form = $('#commentForm');
    const $submitBtn = $form.find('.btn-submit');
    const $commentInput = $form.find('textarea[name="comment"]');
    const $status = $('#comment-status');
    
    const comment = $commentInput.val().trim();
    const videoId = $form.find('input[name="video_id"]').val();
    
    if (!comment) {
        showStatus('Please enter a comment', 'error');
        return false;
    }
    
    // Disable form while submitting
    $submitBtn.prop('disabled', true).text('Posting...');
    
    $.ajax({
        url: 'cfarmtube_comment.php',
        type: 'POST',
        data: {
            video_id: videoId,
            comment: comment
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showStatus('Comment posted successfully!', 'success');
                // Clear the form
                $commentInput.val('');
                // Reload comments section after short delay
                setTimeout(function() {
                    window.location.href = window.location.href;
                }, 1500);
            } else {
                showStatus(response.error || 'Error posting comment', 'error');
            }
        },
        error: function(xhr, status, error) {
            let errorMsg = 'Error connecting to server';
            try {
                const response = JSON.parse(xhr.responseText);
                errorMsg = response.error || errorMsg;
            } catch(e) {
                console.error('Parse error:', e);
            }
            showStatus(errorMsg, 'error');
        },
        complete: function() {
            $submitBtn.prop('disabled', false).text('Comment');
        }
    });
    
    return false;
}

function showStatus(message, type) {
    const $status = $('#comment-status');
    $status
        .removeClass('alert-success alert-error')
        .addClass('alert-' + type)
        .html(message)
        .show();
    
    if (type === 'success') {
        setTimeout(function() {
            $status.fadeOut();
        }, 3000);
    }
}

// Add comment form event handling
$(document).ready(function() {
    // Handle comment input focus
    $('.comment-input').on('focus', function() {
        $('.btn-cancel').show();
    });
    
    // Handle cancel button
    $('.btn-cancel').on('click', function() {
        $('.comment-input').val('').blur();
        $(this).hide();
    }).hide(); // Initially hide cancel button
});
</script>

<?php include("footer.php"); ?>
