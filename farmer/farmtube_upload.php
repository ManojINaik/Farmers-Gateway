<?php
session_start();
require_once("../includes/db.php");
require_once("../includes/function.php");

if(!isset($_SESSION['farmer_login_user'])){
    header("Location: ../index.php");
    exit();
}

$farmer_id = $_SESSION['farmer_login_user'];
$page_title = "Upload Video - FarmTube";
include("fheader.php");

// Handle video upload
if(isset($_POST['uploadVideo'])) {
    $videoUploadDir = "../uploads/videos/";
    $thumbnailUploadDir = "../uploads/thumbnails/";
    
    // Create directories if they don't exist
    if (!file_exists($videoUploadDir)) {
        mkdir($videoUploadDir, 0777, true);
    }
    if (!file_exists($thumbnailUploadDir)) {
        mkdir($thumbnailUploadDir, 0777, true);
    }

    $title = mysqli_real_escape_string($connection, $_POST['title']);
    $description = mysqli_real_escape_string($connection, $_POST['description']);
    $privacy = mysqli_real_escape_string($connection, $_POST['privacy']);
    $category = mysqli_real_escape_string($connection, $_POST['category']);
    
    // Video upload handling
    $videoFile = $_FILES['video'];
    $videoFileName = time() . '_' . basename($videoFile['name']);
    $videoTargetFile = $videoUploadDir . $videoFileName;
    $videoFileType = strtolower(pathinfo($videoTargetFile, PATHINFO_EXTENSION));
    
    // Thumbnail upload handling
    $thumbnailFile = $_FILES['thumbnail'];
    $thumbnailFileName = time() . '_' . basename($thumbnailFile['name']);
    $thumbnailTargetFile = $thumbnailUploadDir . $thumbnailFileName;
    $thumbnailFileType = strtolower(pathinfo($thumbnailTargetFile, PATHINFO_EXTENSION));
    
    // Allowed file types
    $allowedVideoTypes = array('mp4', 'webm', 'ogg');
    $allowedImageTypes = array('jpg', 'jpeg', 'png');
    
    $uploadOk = true;
    $errorMessage = "";
    
    // Validate video
    if (!in_array($videoFileType, $allowedVideoTypes)) {
        $errorMessage .= "Sorry, only MP4, WEBM & OGG files are allowed for videos.<br>";
        $uploadOk = false;
    }
    
    // Validate thumbnail
    if (!in_array($thumbnailFileType, $allowedImageTypes)) {
        $errorMessage .= "Sorry, only JPG, JPEG & PNG files are allowed for thumbnails.<br>";
        $uploadOk = false;
    }
    
    if ($uploadOk) {
        // Upload files
        if (move_uploaded_file($videoFile['tmp_name'], $videoTargetFile) && 
            move_uploaded_file($thumbnailFile['tmp_name'], $thumbnailTargetFile)) {
            
            // Get video duration using getID3
            $getid3_path = "../includes/getid3/getID3-1.9.23/getid3/getid3.php";
            if (file_exists($getid3_path)) {
                require_once($getid3_path);
                $getID3 = new getID3;
                $fileInfo = $getID3->analyze($videoTargetFile);
                $duration = isset($fileInfo['playtime_string']) ? $fileInfo['playtime_string'] : "00:00:00";
            } else {
                // If getID3 is not available, set a default duration
                $duration = "00:00:00";
                error_log("getID3 library not found at: " . $getid3_path);
            }
            
            // Insert into database using prepared statement
            $query = "INSERT INTO farmtube_videos (uploadedBy, userType, title, description, privacy, 
                     filePath, category, duration, thumbnail, uploadDate) 
                     VALUES (?, 'farmer', ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "sssissss", 
                $farmer_id,
                $title,
                $description,
                $privacy,
                $videoFileName,
                $category,
                $duration,
                $thumbnailFileName
            );
            
            if(mysqli_stmt_execute($stmt)) {
                $_SESSION['SuccessMessage'] = "Video uploaded successfully!";
                redirect_to("farmtube.php");
            } else {
                $errorMessage = "Database error: " . mysqli_error($connection);
            }
            mysqli_stmt_close($stmt);
        } else {
            $errorMessage = "Sorry, there was an error uploading your files.";
        }
    }
    
    if(!empty($errorMessage)) {
        $_SESSION['ErrorMessage'] = $errorMessage;
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Navigation -->
        <?php include("fnav.php"); ?>
        
        <!-- Main Content -->
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Upload Video</h1>
            </div>

            <?php
            if(isset($_SESSION['ErrorMessage'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['ErrorMessage'] . '</div>';
                unset($_SESSION['ErrorMessage']);
            }
            if(isset($_SESSION['SuccessMessage'])) {
                echo '<div class="alert alert-success">' . $_SESSION['SuccessMessage'] . '</div>';
                unset($_SESSION['SuccessMessage']);
            }
            ?>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="farmtube_upload.php" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="title">Video Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="Crop Farming">Crop Farming</option>
                                        <option value="Livestock">Livestock</option>
                                        <option value="Organic Farming">Organic Farming</option>
                                        <option value="Farm Equipment">Farm Equipment</option>
                                        <option value="Tips & Tricks">Tips & Tricks</option>
                                        <option value="Success Stories">Success Stories</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="privacy">Privacy</label>
                                    <select class="form-control" id="privacy" name="privacy">
                                        <option value="1">Public</option>
                                        <option value="0">Private</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="video">Video File</label>
                                    <input type="file" class="form-control-file" id="video" name="video" accept="video/*" required>
                                    <small class="form-text text-muted">Supported formats: MP4, WEBM, OGG</small>
                                </div>

                                <div class="form-group">
                                    <label for="thumbnail">Thumbnail Image</label>
                                    <input type="file" class="form-control-file" id="thumbnail" name="thumbnail" accept="image/*" required>
                                    <small class="form-text text-muted">Supported formats: JPG, JPEG, PNG</small>
                                </div>

                                <button type="submit" name="uploadVideo" class="btn btn-primary">Upload Video</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require("../modern-footer.php"); ?>
