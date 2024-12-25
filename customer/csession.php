<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Starting Session
}
require_once('../sql.php');

// Check if user is logged in
if(!isset($_SESSION['customer_login_user'])) {
    header("location: ../index.php");
    exit();
}

// Storing Session
$user_check = $_SESSION['customer_login_user'];

// SQL Query To Fetch Complete Information Of User
$stmt = $conn->prepare("SELECT cust_id, cust_name FROM custlogin WHERE email = ?");
$stmt->bind_param("s", $user_check);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    // User not found in database
    session_destroy();
    header("location: ../index.php");
    exit();
}

$row = $result->fetch_assoc();
$login_session = $row['cust_name'];
$_SESSION['cust_id'] = $row['cust_id'];
$CustID = $user_check;
?>
