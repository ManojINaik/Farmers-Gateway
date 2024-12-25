<?php
session_start();
require('../sql.php');

header('Content-Type: application/json');

try {
    if (isset($_POST['crops']) && isset($_POST['quantity'])) {
        $crop = trim($_POST['crops']);
        $quantity = (int)$_POST['quantity'];
        
        if(empty($crop) || $quantity <= 0) {
            throw new Exception("Invalid input parameters");
        }

        // Use prepared statement to prevent SQL injection
        $query = "SELECT msp FROM farmer_crops_trade WHERE Trade_crop = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $crop);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows === 0) {
            throw new Exception("Crop price not found");
        }
        
        $row = $result->fetch_assoc();
        $price_per_unit = (float)$row["msp"];
        $total_price = $price_per_unit * $quantity;
        
        echo json_encode([
            'success' => true,
            'price' => $total_price,
            'price_per_unit' => $price_per_unit,
            'quantity' => $quantity
        ]);
    } else {
        throw new Exception("Missing required parameters");
    }
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>