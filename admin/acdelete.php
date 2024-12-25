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
        alert('Invalid customer ID');
        window.location='acustomers.php';
    </script>";
    exit();
}

// Sanitize input to prevent SQL injection
$id = mysqli_real_escape_string($conn, $_GET['id']);

// Verify customer exists before deletion
$check_query = "SELECT * FROM custlogin WHERE cust_id = '$id'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) == 0) {
    echo "<script>
        alert('Customer not found');
        window.location='acustomers.php';
    </script>";
    exit();
}

// Begin transaction for safe deletion
mysqli_begin_transaction($conn);

try {
    // Delete related records first (optional, depending on your database structure)
    // For example, delete customer's orders, comments, etc.
    // $delete_orders = "DELETE FROM orders WHERE customer_id = '$id'";
    // mysqli_query($conn, $delete_orders);

    // Delete customer
    $delete_query = "DELETE FROM custlogin WHERE cust_id = '$id'";
    $result = mysqli_query($conn, $delete_query);

    if ($result) {
        // Commit transaction
        mysqli_commit($conn);
        
        echo "<script>
            alert('Customer Deleted Successfully');
            window.location='acustomers.php';
        </script>";
        exit();
    } else {
        // Rollback transaction
        mysqli_rollback($conn);
        
        echo "<script>
            alert('Error deleting customer: " . mysqli_error($conn) . "');
            window.location='acustomers.php';
        </script>";
        exit();
    }
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    
    echo "<script>
        alert('An unexpected error occurred: " . $e->getMessage() . "');
        window.location='acustomers.php';
    </script>";
    exit();
}

mysqli_close($conn);
?>