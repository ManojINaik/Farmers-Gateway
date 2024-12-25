<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/function.php");

if(!isset($_SESSION['customer_login_user'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['customer_login_user'];
include("cheader.php");

// Handle search
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Get all categories
$categories_query = "SELECT DISTINCT category FROM farmtube_videos WHERE category IS NOT NULL ORDER BY category";
$categories_result = mysqli_query($connection, $categories_query);
if (!$categories_result) {
    die("Error fetching categories: " . mysqli_error($connection));
}
$categories = [];
while ($row = mysqli_fetch_assoc($categories_result)) {
    if (!empty($row['category'])) {
        $categories[] = $row['category'];
    }
}

// Build video query with search and filter
$base_query = "SELECT v.*, 
    CASE 
        WHEN v.userType = 'farmer' THEN f.farmer_name
        WHEN v.userType = 'customer' THEN c.cust_name
        WHEN v.userType = 'admin' THEN a.admin_name
        ELSE 'Unknown'
    END as uploader_name,
    v.likes as likes_count
    FROM farmtube_videos v 
    LEFT JOIN farmerlogin f ON v.uploadedBy = f.email AND v.userType = 'farmer'
    LEFT JOIN custlogin c ON v.uploadedBy = c.email AND v.userType = 'customer'
    LEFT JOIN admin a ON v.uploadedBy = a.admin_name AND v.userType = 'admin'
    WHERE 1=1";

$params = [];
$types = "";

if (!empty($search_query)) {
    $base_query .= " AND (v.title LIKE ? OR v.description LIKE ?)";
    $search_param = "%" . mysqli_real_escape_string($connection, $search_query) . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($category_filter)) {
    $base_query .= " AND v.category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

$base_query .= " ORDER BY v.uploadDate DESC";

// Prepare and execute the statement
$stmt = mysqli_prepare($connection, $base_query);
if ($stmt === false) {
    die("Error preparing statement: " . mysqli_error($connection));
}

if (!empty($params)) {
    if (!mysqli_stmt_bind_param($stmt, $types, ...$params)) {
        die("Error binding parameters: " . mysqli_stmt_error($stmt));
    }
}

if (!mysqli_stmt_execute($stmt)) {
    die("Error executing statement: " . mysqli_stmt_error($stmt));
}

$result = mysqli_stmt_get_result($stmt);
if ($result === false) {
    die("Error getting result set: " . mysqli_stmt_error($stmt));
}

// Function to format time ago
function time_elapsed_string($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}
?>

<!-- Custom Styles -->
<style>
:root {
    --primary-color: #4CAF50;
    --primary-dark: #388E3C;
    --primary-light: rgba(200, 230, 201, 0.1);
    --accent-color: #8BC34A;
    --text-primary: #E0E0E0;
    --text-secondary: #9E9E9E;
    --bg-primary: #1E1E1E;
    --bg-secondary: #2D2D2D;
    --bg-accent: rgba(76, 175, 80, 0.1);
    --border-color: #404040;
    --radius-sm: 4px;
    --radius-md: 8px;
    --radius-lg: 16px;
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.2);
    --shadow-md: 0 4px 8px rgba(0,0,0,0.3);
    --shadow-lg: 0 8px 16px rgba(0,0,0,0.4);
}

body {
    background-color: var(--bg-primary);
    color: var(--text-primary);
}

/* Container Layout */
.main-content {
    padding: 2rem 0;
    background: var(--bg-primary);
}

.container-fluid {
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* Search Container */
.search-container {
    max-width: 800px;
    margin: 2rem auto 3rem;
    padding: 2rem;
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    position: relative;
}

.search-form {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.search-input-container {
    flex-grow: 1;
    position: relative;
}

.search-input {
    width: 100%;
    padding: 1rem 1.5rem;
    padding-left: 3rem;
    background: var(--bg-primary);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    color: var(--text-primary);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
    outline: none;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
    pointer-events: none;
}

.search-button {
    padding: 1rem 2rem;
    background: var(--primary-color);
    border: none;
    border-radius: var(--radius-md);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.search-button:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.search-button:active {
    transform: translateY(0);
}

.search-button i {
    font-size: 1.1rem;
}

/* Category Filter */
.category-filter {
    margin-top: 1rem;
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.category-tag {
    padding: 0.5rem 1rem;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.3s ease;
}

.category-tag:hover,
.category-tag.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

/* Videos Grid */
.videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    padding: 0 1rem;
}

/* Video Card */
.video-card {
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.video-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    background: var(--bg-accent);
}

.thumbnail-container {
    position: relative;
    width: 100%;
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
    background: #000;
    overflow: hidden;
}

.video-thumbnail {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.video-info {
    padding: 1.25rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.video-title {
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.4;
    margin-bottom: 0.75rem;
    color: var(--text-primary);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.video-meta {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.uploader {
    font-weight: 500;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.uploader i {
    font-size: 1.1rem;
    color: var(--primary-color);
}

.video-stats {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.video-stats span {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.video-stats i {
    font-size: 1rem;
    opacity: 0.8;
}

.upload-time {
    color: var(--text-secondary);
    font-size: 0.85rem;
    margin-top: 0.5rem;
}

/* Responsive Design */
@media (max-width: 1400px) {
    .videos-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 0 1rem;
    }
    
    .videos-grid {
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1rem;
    }

    .video-title {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .videos-grid {
        grid-template-columns: 1fr;
        padding: 0 0.5rem;
    }

    .video-info {
        padding: 1rem;
    }
}
</style>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <!-- Navigation -->
        <?php include("cnav.php"); ?>
        
        <!-- Search Bar -->
        <div class="search-container">
            <form method="GET" action="" class="search-form">
                <div class="search-input-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="search-input" placeholder="Search for farming videos..." value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                    Search
                </button>
            </form>
            <?php if (!empty($categories)): ?>
            <div class="category-filter">
                <a href="?<?php echo !empty($search_query) ? 'search='.urlencode($search_query) : ''; ?>" 
                   class="category-tag <?php echo empty($category_filter) ? 'active' : ''; ?>">
                    All
                </a>
                <?php foreach($categories as $category): ?>
                <a href="?<?php echo !empty($search_query) ? 'search='.urlencode($search_query).'&' : ''; ?>category=<?php echo urlencode($category); ?>" 
                   class="category-tag <?php echo $category_filter === $category ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($category); ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Videos Grid -->
        <div class="videos-grid">
            <?php
            // Fetch videos with uploader information
            $videos_query = "SELECT v.*, 
                CASE 
                    WHEN v.userType = 'farmer' THEN f.farmer_name
                    WHEN v.userType = 'customer' THEN c.cust_name
                    WHEN v.userType = 'admin' THEN a.admin_name
                    ELSE 'Unknown'
                END as uploader_name,
                (SELECT COUNT(*) FROM farmtube_comments WHERE videoId = v.id) as comment_count
                FROM farmtube_videos v 
                LEFT JOIN farmerlogin f ON v.uploadedBy = f.email AND v.userType = 'farmer'
                LEFT JOIN custlogin c ON v.uploadedBy = c.email AND v.userType = 'customer'
                LEFT JOIN admin a ON v.uploadedBy = a.admin_name AND v.userType = 'admin'
                WHERE v.privacy = 1";

            // Add search condition if search term exists
            if(isset($_GET['search']) && !empty($_GET['search'])) {
                $search_term = '%' . mysqli_real_escape_string($connection, $_GET['search']) . '%';
                $videos_query .= " AND (v.title LIKE ? OR v.description LIKE ?)";
            }

            $videos_query .= " ORDER BY v.uploadDate DESC";

            if($stmt = mysqli_prepare($connection, $videos_query)) {
                if(isset($search_term)) {
                    mysqli_stmt_bind_param($stmt, "ss", $search_term, $search_term);
                }
                
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if(mysqli_num_rows($result) > 0) {
                    while($video = mysqli_fetch_assoc($result)) {
                        // Calculate time ago
                        $upload_date = new DateTime($video['uploadDate']);
                        $now = new DateTime();
                        $interval = $now->diff($upload_date);
                        
                        if ($interval->y > 0) {
                            $time_ago = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
                        } elseif ($interval->m > 0) {
                            $time_ago = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
                        } elseif ($interval->d > 0) {
                            $time_ago = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
                        } elseif ($interval->h > 0) {
                            $time_ago = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
                        } elseif ($interval->i > 0) {
                            $time_ago = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
                        } else {
                            $time_ago = 'Just now';
                        }
                        ?>
                        <div class="video-card">
                            <a href="cfarmtube_watch.php?v=<?php echo $video['id']; ?>" class="video-link">
                                <div class="thumbnail-container">
                                    <img src="<?php echo '../uploads/thumbnails/' . htmlspecialchars($video['thumbnail']); ?>" 
                                         alt="<?php echo htmlspecialchars($video['title']); ?>"
                                         class="video-thumbnail"
                                         onerror="this.src='../assets/img/default-thumbnail.jpg'"
                                    >
                                    <span class="duration">
                                        <?php 
                                            echo isset($video['duration']) ? htmlspecialchars($video['duration']) : '0:00'; 
                                        ?>
                                    </span>
                                </div>
                                <div class="video-info">
                                    <div>
                                        <h3 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                                        <div class="video-meta">
                                            <span class="uploader">
                                                <i class="fas fa-user-circle"></i>
                                                <?php echo htmlspecialchars($video['uploader_name']); ?>
                                            </span>
                                            <div class="video-stats">
                                                <span class="views">
                                                    <i class="fas fa-eye"></i>
                                                    <?php echo number_format($video['views']); ?>
                                                </span>
                                                <span class="likes">
                                                    <i class="fas fa-thumbs-up"></i>
                                                    <?php echo number_format($video['likes']); ?>
                                                </span>
                                                <span class="comments">
                                                    <i class="fas fa-comment"></i>
                                                    <?php echo number_format($video['comment_count']); ?>
                                                </span>
                                            </div>
                                            <span class="upload-time"><?php echo $time_ago; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="no-videos">No videos found</div>';
                }
                mysqli_stmt_close($stmt);
            }
            ?>
        </div>
    </div>
</div>

<?php include("../modern-footer.php"); ?>
