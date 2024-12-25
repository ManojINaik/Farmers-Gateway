<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

include("../Includes/db.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    if (!isset($_POST['request_id']) || !isset($_POST['status'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $request_id = mysqli_real_escape_string($connection, $_POST['request_id']);
    $status = mysqli_real_escape_string($connection, $_POST['status']);

    // Validate status
    $allowed_statuses = ['Approved', 'Rejected'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }

    // Update the request status
    $query = "UPDATE crop_requests SET status = ?, updated_at = NOW() WHERE request_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $request_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // If status is approved, create entry in crop_trades
        if ($status === 'Approved') {
            // Get request details
            $request_query = "SELECT farmer_id, crop_name, quantity, price_per_kg FROM crop_requests WHERE request_id = ?";
            $request_stmt = mysqli_prepare($connection, $request_query);
            mysqli_stmt_bind_param($request_stmt, "i", $request_id);
            mysqli_stmt_execute($request_stmt);
            $request_result = mysqli_stmt_get_result($request_stmt);
            $request_data = mysqli_fetch_assoc($request_result);

            if ($request_data) {
                // Insert into farmer_crops
                $total_amount = $request_data['quantity'] * $request_data['price_per_kg'];
                $inventory_query = "INSERT INTO farmer_crops (farmer_id, crop_name, quantity, price_per_kg) 
                                  VALUES (?, ?, ?, ?) 
                                  ON DUPLICATE KEY UPDATE 
                                  quantity = quantity + VALUES(quantity)";
                $inventory_stmt = mysqli_prepare($connection, $inventory_query);
                mysqli_stmt_bind_param($inventory_stmt, "isdd", 
                    $request_data['farmer_id'], 
                    $request_data['crop_name'],
                    $request_data['quantity'],
                    $request_data['price_per_kg']
                );
                mysqli_stmt_execute($inventory_stmt);
            }
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
