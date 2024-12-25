<?php
include("asession.php");
include("../Includes/db.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    if (!isset($_POST['trade_id']) || !isset($_POST['status'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $trade_id = mysqli_real_escape_string($connection, $_POST['trade_id']);
    $status = mysqli_real_escape_string($connection, $_POST['status']);

    // Validate status
    $allowed_statuses = ['Completed', 'Cancelled'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }

    // Update the trade status
    $query = "UPDATE crop_trades SET status = ?, updated_at = NOW() WHERE trade_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $trade_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // If status is completed, update inventory
        if ($status === 'Completed') {
            // Get trade details
            $trade_query = "SELECT farmer_id, crop_name, quantity FROM crop_trades WHERE trade_id = ?";
            $trade_stmt = mysqli_prepare($connection, $trade_query);
            mysqli_stmt_bind_param($trade_stmt, "i", $trade_id);
            mysqli_stmt_execute($trade_stmt);
            $trade_result = mysqli_stmt_get_result($trade_stmt);
            $trade_data = mysqli_fetch_assoc($trade_result);

            if ($trade_data) {
                // Update farmer's crop inventory
                $inventory_query = "UPDATE farmer_crops 
                                  SET quantity = quantity - ? 
                                  WHERE farmer_id = ? AND crop_name = ?";
                $inventory_stmt = mysqli_prepare($connection, $inventory_query);
                mysqli_stmt_bind_param($inventory_stmt, "dis", 
                    $trade_data['quantity'], 
                    $trade_data['farmer_id'], 
                    $trade_data['crop_name']
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
