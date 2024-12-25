<?php
session_start();
require('../sql.php');
require('cart_init.php');

// Prevent direct access to this file
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header('Location: cbuy_crops.php');
    exit();
}

// Initialize cart
initializeCart($conn);

if(isset($_POST['add_to_cart'])) {
    $crop = $_POST['crops'];
    $quantity = (float)$_POST['quantity'];
    $cust_id = $_SESSION['cust_id'];

    if(!$cust_id) {
        echo json_encode(['success' => false, 'message' => 'Please log in first']);
        exit();
    }

    if(empty($crop) || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid crop or quantity']);
        exit();
    }

    // First check if this crop is still available and get its price
    $check_query = "SELECT quantity, price_per_kg FROM production_approx WHERE crop = ? FOR UPDATE";
    
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("s", $crop);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if(!$row) {
            throw new Exception('Crop not found');
        }
        
        if($row['quantity'] < $quantity) {
            throw new Exception('Not enough stock available');
        }
        
        $price_per_kg = $row['price_per_kg'];
        $total_price = $quantity * $price_per_kg;
        
        // Check if item already exists in cart
        $cart_check = "SELECT id, quantity FROM cart WHERE cropname = ? AND cust_id = ?";
        $stmt = $conn->prepare($cart_check);
        $stmt->bind_param("si", $crop, $cust_id);
        $stmt->execute();
        $cart_result = $stmt->get_result();
        $cart_item = $cart_result->fetch_assoc();
        
        if($cart_item) {
            // Update existing cart item
            $new_quantity = $cart_item['quantity'] + $quantity;
            if($new_quantity > $row['quantity']) {
                throw new Exception('Total quantity exceeds available stock');
            }
            
            $update_cart = "UPDATE cart SET quantity = ?, price = ? WHERE id = ?";
            $stmt = $conn->prepare($update_cart);
            $stmt->bind_param("ddi", $new_quantity, $price_per_kg, $cart_item['id']);
            $stmt->execute();
        } else {
            // Add new item to cart
            $insert_cart = "INSERT INTO cart (cust_id, cropname, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_cart);
            $stmt->bind_param("isdd", $cust_id, $crop, $quantity, $price_per_kg);
            $stmt->execute();
        }
        
        // Update stock quantity
        $update_stock = "UPDATE production_approx SET quantity = quantity - ? WHERE crop = ?";
        $stmt = $conn->prepare($update_stock);
        $stmt->bind_param("ds", $quantity, $crop);
        $stmt->execute();
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Item added to cart successfully']);
        
    } catch(Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit();
}

// Handle delete action
if(isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
    $id = $_GET["id"];
    
    try {
        $conn->begin_transaction();
        
        // Get item details before deletion
        $get_item = "SELECT cropname, quantity FROM cart WHERE id = ? AND cust_id = ?";
        $stmt = $conn->prepare($get_item);
        $stmt->bind_param("ii", $id, $_SESSION['cust_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        
        if($item) {
            // Return quantity to stock
            $update_stock = "UPDATE production_approx SET quantity = quantity + ? WHERE crop = ?";
            $stmt = $conn->prepare($update_stock);
            $stmt->bind_param("ds", $item['quantity'], $item['cropname']);
            $stmt->execute();
            
            // Delete from cart
            $delete_item = "DELETE FROM cart WHERE id = ? AND cust_id = ?";
            $stmt = $conn->prepare($delete_item);
            $stmt->bind_param("ii", $id, $_SESSION['cust_id']);
            $stmt->execute();
            
            $conn->commit();
            header("Location: cbuy_crops.php?status=deleted");
        } else {
            throw new Exception("Item not found in cart");
        }
    } catch(Exception $e) {
        $conn->rollback();
        header("Location: cbuy_crops.php?error=" . urlencode($e->getMessage()));
    }
    exit();
}

// If no valid action is specified
header("Location: cbuy_crops.php");
exit();
?>