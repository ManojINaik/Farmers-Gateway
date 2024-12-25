<?php
include ('fsession.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['farmer_login_user'])){
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $crop_name = mysqli_real_escape_string($conn, $_POST['crop_name']);
        $quantity = (int)$_POST['quantity'];
        
        // Check if crop already exists
        $check_sql = "SELECT COUNT(*) as count FROM production_approx WHERE crop = '$crop_name'";
        $check_result = mysqli_query($conn, $check_sql);
        $row = mysqli_fetch_assoc($check_result);
        
        if ($row['count'] > 0) {
            // Update existing crop
            $sql = "UPDATE production_approx SET quantity = quantity + $quantity WHERE crop = '$crop_name'";
        } else {
            // Insert new crop
            $sql = "INSERT INTO production_approx (crop, quantity) VALUES ('$crop_name', $quantity)";
        }
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        }
        break;
        
    case 'edit':
        $original_crop_name = mysqli_real_escape_string($conn, $_POST['original_crop_name']);
        $new_crop_name = mysqli_real_escape_string($conn, $_POST['crop_name']);
        $quantity = (int)$_POST['quantity'];
        
        // Update crop
        $sql = "UPDATE production_approx SET crop = '$new_crop_name', quantity = $quantity WHERE crop = '$original_crop_name'";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        }
        break;
        
    case 'delete':
        $crop_name = mysqli_real_escape_string($conn, $_POST['crop_name']);
        
        // Delete crop
        $sql = "DELETE FROM production_approx WHERE crop = '$crop_name'";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
