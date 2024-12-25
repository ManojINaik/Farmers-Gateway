<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['customer_login_user']) && !empty($_SESSION['customer_login_user']);
}

// Check if user is logged in, if not redirect to login page
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

// Get customer ID if not already set
if (!isset($_SESSION['cust_id']) && isset($_SESSION['customer_login_user'])) {
    require_once('db.php');
    $query = "SELECT cust_id FROM custlogin WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $_SESSION['customer_login_user']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['cust_id'] = $row['cust_id'];
    }
}

// Ensure customer ID is set
if (!isset($_SESSION['cust_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
