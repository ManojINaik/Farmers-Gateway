<?php
include('fsession.php');
require_once("../includes/db.php");
$farmer_email = $_SESSION['farmer_login_user'];
include('fheader.php');
include('fnav.php');

if (isset($_POST['submit_discussion'])) {
    $title = mysqli_real_escape_string($connection, $_POST['title']);
    $content = mysqli_real_escape_string($connection, $_POST['content']);
    $query = "INSERT INTO farmer_discussions (farmer_email, title, content) VALUES ('$farmer_email', '$title', '$content')";
    if (!mysqli_query($connection, $query)) {
        echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Error posting discussion: " . mysqli_error($connection) . "</div>";
    }
}

if (isset($_POST['submit_reply'])) {
    $content = mysqli_real_escape_string($connection, $_POST['reply_content']);
    $discussion_id = mysqli_real_escape_string($connection, $_POST['discussion_id']);
    $query = "INSERT INTO discussion_replies (discussion_id, farmer_email, content) VALUES ('$discussion_id', '$farmer_email', '$content')";
    if (!mysqli_query($connection, $query)) {
        echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Error posting reply: " . mysqli_error($connection) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <title>Farmer Discussion Forum</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2C5F2D;
            --secondary-color: #97BC62;
            --accent-color: #DAE5D0;
            --text-color: #1A1A1A;
            --light-color: #F5F5F5;
        }

        body {
            background-color: var(--light-color);
            color: var(--text-color);
        }

        .container {
            max-width: 1200px;
            padding: 2rem;
        }

        .forum-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            animation: slideDown 0.5s ease-out;
        }

        .discussion-card {
            background: white;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeIn 0.5s ease-out;
        }

        .discussion-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .reply-section {
            margin-left: 3rem;
            border-left: 3px solid var(--accent-color);
            padding-left: 1.5rem;
            margin-top: 1rem;
        }

        .reply-card {
            background: var(--light-color);
            border-radius: 8px;
            margin-bottom: 1rem;
            padding: 1rem;
            transition: transform 0.2s ease;
        }

        .reply-card:hover {
            transform: scale(1.01);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
        }

        .form-control {
            border: 2px solid var(--accent-color);
            border-radius: 8px;
            padding: 0.8rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(151, 188, 98, 0.25);
        }

        .discussion-meta {
            font-size: 0.9rem;
            color: #666;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .discussion-meta i {
            color: var(--secondary-color);
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .new-discussion-form {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            animation: slideDown 0.6s ease-out;
        }

        .form-label {
            color: var(--primary-color);
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
            animation: slideDown 0.4s ease-out;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="forum-header">
        <h1><i class="fas fa-comments"></i> Farmer Discussion Forum</h1>
        <p class="mb-0">Connect, Share, and Learn with Fellow Farmers</p>
    </div>
    
    <!-- New Discussion Form -->
    <div class="new-discussion-form">
        <h5 class="card-title"><i class="fas fa-plus-circle"></i> Start New Discussion</h5>
        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required 
                       placeholder="What would you like to discuss?">
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="3" required
                          placeholder="Share your thoughts, questions, or experiences..."></textarea>
            </div>
            <button type="submit" name="submit_discussion" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Post Discussion
            </button>
        </form>
    </div>

    <!-- Existing Discussions -->
    <?php
    $query = "SELECT d.*, f.farmer_name FROM farmer_discussions d 
              JOIN farmerlogin f ON d.farmer_email = f.email 
              ORDER BY d.created_at DESC";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Error loading discussions: " . mysqli_error($connection) . "</div>";
    }

    while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="discussion-card">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                <div class="discussion-meta mb-3">
                    <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($row['farmer_name']); ?></span>
                    <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
                </div>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>

                <!-- Replies -->
                <div class="reply-section">
                    <?php
                    $discussion_id = $row['id'];
                    $reply_query = "SELECT r.*, f.farmer_name FROM discussion_replies r 
                                  JOIN farmerlogin f ON r.farmer_email = f.email 
                                  WHERE r.discussion_id = $discussion_id 
                                  ORDER BY r.created_at ASC";
                    $reply_result = mysqli_query($connection, $reply_query);
                    if (!$reply_result) {
                        echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Error loading replies: " . mysqli_error($connection) . "</div>";
                    }

                    while ($reply = mysqli_fetch_assoc($reply_result)) {
                        ?>
                        <div class="reply-card">
                            <div class="discussion-meta mb-2">
                                <span><i class="fas fa-reply"></i> <?php echo htmlspecialchars($reply['farmer_name']); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo date('M d, Y', strtotime($reply['created_at'])); ?></span>
                            </div>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($reply['content'])); ?></p>
                        </div>
                        <?php
                    }
                    ?>

                    <!-- Reply Form -->
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="discussion_id" value="<?php echo $discussion_id; ?>">
                        <div class="mb-3">
                            <textarea class="form-control" name="reply_content" rows="2" 
                                      placeholder="Share your thoughts on this discussion..." required></textarea>
                        </div>
                        <button type="submit" name="submit_reply" class="btn btn-secondary">
                            <i class="fas fa-reply"></i> Reply
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include("../modern-footer.php"); ?>