<?php
session_start();
require('../sql.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_login_user'])) {
    header("Location: ../index.php");
    exit();
}

// Check if an ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
        alert('Invalid farmer ID');
        window.location='afarmers.php';
    </script>";
    exit();
}

// Sanitize input to prevent SQL injection
$id = mysqli_real_escape_string($conn, $_GET['id']);

// Verify farmer exists before deletion
$check_query = "SELECT * FROM farmerlogin WHERE farmer_id = '$id'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) == 0) {
    echo "<script>
        alert('Farmer not found');
        window.location='afarmers.php';
    </script>";
    exit();
}

// Begin transaction for safe deletion
mysqli_begin_transaction($conn);

try {
    // Delete related records first (optional, depending on your database structure)
    // For example, delete farmer's products, orders, etc.
    // $delete_products = "DELETE FROM products WHERE farmer_id = '$id'";
    // mysqli_query($conn, $delete_products);

    // Delete farmer
    $delete_query = "DELETE FROM farmerlogin WHERE farmer_id = '$id'";
    $result = mysqli_query($conn, $delete_query);

    if ($result) {
        // Commit transaction
        mysqli_commit($conn);
        
        echo "<script>
            alert('Farmer Deleted Successfully');
            window.location='afarmers.php';
        </script>";
        exit();
    } else {
        // Rollback transaction
        mysqli_rollback($conn);
        
        echo "<script>
            alert('Error deleting farmer: " . mysqli_error($conn) . "');
            window.location='afarmers.php';
        </script>";
        exit();
    }
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    
    echo "<script>
        alert('An unexpected error occurred: " . $e->getMessage() . "');
        window.location='afarmers.php';
    </script>";
    exit();
}

mysqli_close($conn);
?>
