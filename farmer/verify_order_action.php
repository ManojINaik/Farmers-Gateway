<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('../sql.php');
require_once('fsession.php');

// Check if user is logged in
if (!isset($_SESSION['farmer_login_user'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Not authorized']);
    exit;
}

// Get farmer ID
$user_check = $_SESSION['farmer_login_user'];
$query = "SELECT farmer_id FROM farmerlogin WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_check);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$farmer_id = $row['farmer_id'];

// Check if POST data is set
if (!isset($_POST['order_id']) || !isset($_POST['action'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit;
}

$order_id = $_POST['order_id'];
$action = $_POST['action'];

// Verify that this order belongs to the logged-in farmer
$check_query = "SELECT o.* FROM orders o 
                JOIN order_history oh ON o.order_id = oh.order_id 
                WHERE o.order_id = ? AND oh.farmer_id = ? AND o.status = 'pending'
                LIMIT 1";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("si", $order_id, $farmer_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Order not found or already processed']);
    exit;
}

try {
    // Begin transaction
    $conn->begin_transaction();

    // Update order status
    $new_status = ($action === 'verify') ? 'verified' : 'cancelled';
    $update_query = "UPDATE orders SET status = ? WHERE order_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ss", $new_status, $order_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception("Failed to update order status");
    }

    // If cancelling, restore the crop quantity
    if ($action === 'cancel') {
        $restore_query = "UPDATE farmer_crops_trade fct 
                         JOIN order_history oh ON LOWER(TRIM(fct.Trade_crop)) = LOWER(TRIM(oh.crop_name))
                         SET fct.Crop_quantity = fct.Crop_quantity + oh.quantity
                         WHERE oh.order_id = ? AND oh.farmer_id = fct.farmer_fkid";
        $restore_stmt = $conn->prepare($restore_query);
        $restore_stmt->bind_param("s", $order_id);
        
        if (!$restore_stmt->execute()) {
            throw new Exception("Failed to restore crop quantity");
        }
    }

    // Commit transaction
    $conn->commit();

    // Send success response
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'message' => ($action === 'verify') ? 'Order verified successfully' : 'Order cancelled successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    // Log error
    error_log("Order processing error: " . $e->getMessage());
    
    // Send error response
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
