<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/function.php");

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

$page_title = "FarmTube - Agriculture Knowledge Sharing";
?>

<style>
.main-content {
    padding: 2rem;
    min-height: calc(100vh - 60px);
    background: #0f0f0f;
}

/* Title Section */
.farmtube-title {
    color: #6c757d;
    font-size: 2rem;
    margin-bottom: 1.5rem;
}

/* Upload Button */
.upload-btn {
    position: fixed;
    right: 2rem;
    top: 100px;
    background: #4CAF50;
    color: white;
    padding: 0.8rem 1.5rem;
    border-radius: 4px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.upload-btn:hover {
    background: #388E3C;
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}

.upload-btn i {
    font-size: 1.2rem;
}

/* Category Tabs */
.category-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
}

.category-tab {
    padding: 0.5rem 1.5rem;
    background: #2d2d2d;
    color: #aaa;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.category-tab:hover,
.category-tab.active {
    background: #4CAF50;
    color: white;
}

/* Video Grid */
.videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.video-card {
    background: #1e1e1e;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s;
}

.video-card:hover {
    transform: translateY(-4px);
}

.video-link {
    text-decoration: none;
    color: inherit;
}

.thumbnail-container {
    position: relative;
    width: 100%;
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
    background: #000;
}

.video-thumbnail {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-duration {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 2px 4px;
    border-radius: 2px;
    font-size: 12px;
}

.video-info {
    padding: 12px;
}

.video-title {
    font-size: 14px;
    font-weight: 500;
    color: #fff;
    margin: 0 0 8px 0;
    line-height: 1.2;
}

.channel-info {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
}

.channel-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #2d2d2d;
    display: flex;
    align-items: center;
    justify-content: center;
}

.channel-name {
    color: #aaa;
    font-size: 13px;
}

.video-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    color: #aaa;
    font-size: 13px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Search Panel */
.search-panel {
    margin: 1rem 0 2rem 0;
}

.search-form {
    width: 100%;
}

.search-container {
    position: relative;
    display: flex;
    align-items: center;
    max-width: 800px;
    margin: 0 auto;
}

.search-icon {
    position: absolute;
    left: 1rem;
    color: #aaa;
}

.search-input {
    flex-grow: 1;
    padding: 0.8rem 1rem 0.8rem 2.5rem;
    border: 1px solid #303030;
    border-radius: 4px 0 0 4px;
    background: #121212;
    color: white;
    font-size: 1rem;
}

.search-input:focus {
    outline: none;
    border-color: #4CAF50;
}

.search-button {
    padding: 0.8rem 1.5rem;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 0 4px 4px 0;
    cursor: pointer;
    transition: background-color 0.2s;
}

.search-button:hover {
    background: #388E3C;
}

/* No Results Message */
.no-results {
    text-align: center;
    color: #aaa;
    padding: 2rem;
    grid-column: 1 / -1;
}

@media (max-width: 768px) {
    .videos-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .search-form {
        flex-direction: column;
    }
    
    .search-button {
        width: 100%;
    }
}
</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarmTube</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-white" id="top">
<?php include ('fnav.php'); ?>
<script>
    $(document).ready(function(){
        $('.dropdown-toggle').dropdown();
    });
</script>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="farmtube-title">FarmTube</h1>
        <?php if($user_type == 'farmer'): ?>
        <a href="farmtube_upload.php" class="upload-btn">
            <i class="fas fa-upload"></i>
            UPLOAD VIDEO
        </a>
        <?php endif; ?>
    </div>

    <!-- Search Panel -->
    <div class="search-panel">
        <form action="farmtube.php" method="GET" class="search-form">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" placeholder="Search for farming videos..." 
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
                    class="search-input">
                <button type="submit" class="search-button">Search</button>
            </div>
        </form>
    </div>

    <!-- Category Tabs -->
    <div class="category-tabs">
        <a href="farmtube.php" class="category-tab <?php echo !isset($_GET['category']) ? 'active' : ''; ?>">ALL</a>
        <?php
        $categories_query = "SELECT DISTINCT category FROM farmtube_videos WHERE privacy = 1";
        $categories = mysqli_query($connection, $categories_query);
        while($category = mysqli_fetch_assoc($categories)) {
            $active = isset($_GET['category']) && $_GET['category'] == $category['category'] ? 'active' : '';
            echo '<a href="farmtube.php?category='.urlencode($category['category']).'" class="category-tab '.$active.'">'.$category['category'].'</a>';
        }
        ?>
    </div>

    <!-- Videos Grid -->
    <div class="videos-grid">
        <?php
        // Build the SQL query based on search and category filters
        $sql = "SELECT v.*, 
                 CASE 
                    WHEN v.userType = 'farmer' THEN f.farmer_name
                    WHEN v.userType = 'customer' THEN c.cust_name
                    WHEN v.userType = 'admin' THEN a.admin_name
                 END as uploader_name
                 FROM farmtube_videos v 
                 LEFT JOIN farmerlogin f ON v.uploadedBy = f.email AND v.userType = 'farmer'
                 LEFT JOIN custlogin c ON v.uploadedBy = c.email AND v.userType = 'customer'
                 LEFT JOIN admin a ON v.uploadedBy = a.admin_name AND v.userType = 'admin'
                 WHERE v.privacy = 1";
        
        // Add search condition if search parameter exists
        if(isset($_GET['search']) && !empty($_GET['search'])) {
            $search = mysqli_real_escape_string($connection, $_GET['search']);
            $sql .= " AND (v.title LIKE '%$search%' OR v.description LIKE '%$search%')";
        }
        
        // Add category condition if category parameter exists
        if(isset($_GET['category'])) {
            $category = mysqli_real_escape_string($connection, $_GET['category']);
            $sql .= " AND v.category = '$category'";
        }
        
        $sql .= " ORDER BY v.uploadDate DESC";
        
        $result = mysqli_query($connection, $sql);
        
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $thumbnail = $row['thumbnail'];
                $duration = $row['duration'];
                $title = $row['title'];
                $uploader = $row['uploader_name'] ?: 'Unknown User';
                $views = $row['views'];
                $uploadDate = date("M j, Y", strtotime($row['uploadDate']));
                $timeAgo = time_elapsed_string($row['uploadDate']);
                
                echo '<div class="video-card">
                        <a href="farmtube_watch.php?id='.$row['id'].'" class="video-link">
                            <div class="thumbnail-container">
                                <img src="../uploads/thumbnails/'.$thumbnail.'" 
                                     alt="'.htmlspecialchars($row['title']).'" 
                                     class="video-thumbnail">
                                <div class="video-duration">'.$duration.'</div>
                            </div>
                            <div class="video-info">
                                <h3 class="video-title">'.htmlspecialchars($row['title']).'</h3>
                                <div class="channel-info">
                                    <div class="channel-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="channel-name">'.htmlspecialchars($row['uploader_name']).'</span>
                                </div>
                                <div class="video-meta">
                                    <span class="meta-item">'.number_format($row['views']).' views</span>
                                    <span class="meta-item">'.time_elapsed_string($row['uploadDate']).'</span>
                                </div>
                            </div>
                        </a>
                    </div>';
            }
        } else {
            echo '<div class="no-results">No videos found</div>';
        }
        ?>
    </div>
</div>

<?php 
// Include appropriate footer based on user type
// if($user_type == 'farmer') {
//     include("footer.php");
// } elseif($user_type == 'customer') {
//     include("../customer/footer.php");
// } else {
//     include("../admin/footer.php");
// }
?>

<?php
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>
<?php include("../modern-footer.php"); ?>
</body>
</html>