<?php
session_start();
require('../sql.php');

header('Content-Type: application/json');

try {
    if (!isset($_POST['crops'])) {
        throw new Exception("Crop parameter is required");
    }

    $crop = trim($_POST['crops']);
    if (empty($crop)) {
        throw new Exception("Crop name cannot be empty");
    }

    // Get quantity from production_approx
    $query = "SELECT quantity FROM production_approx WHERE crop = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $crop);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Crop not found");
    }
    
    $row = $result->fetch_assoc();
    $quantity = (int)$row["quantity"];

    // Get trade ID from farmer_crops_trade
    $query = "SELECT trade_id FROM farmer_crops_trade WHERE Trade_crop = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $crop);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Trade information not found");
    }
    
    $row = $result->fetch_assoc();
    $tradeId = (int)$row["trade_id"];

    echo json_encode([
        'success' => true,
        'TradeIdR' => $tradeId,
        'quantityR' => $quantity
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>