<?php
session_start();
require('../sql.php');
require('cart_init.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if(!isset($_SESSION['cust_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to continue']);
    exit();
}

// Prevent direct access
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    header('Location: cbuy_crops.php');
    exit();
}

// Simple payment simulation without Razorpay for testing
if(isset($_POST['create_order'])) {
    $cust_id = $_SESSION['cust_id'];
    
    try {
        // Calculate total amount
        $total_query = "SELECT SUM(quantity * price) as total FROM cart WHERE cust_id = ?";
        $stmt = $conn->prepare($total_query);
        $stmt->bind_param("i", $cust_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if(!$row || $row['total'] <= 0) {
            throw new Exception('Cart is empty');
        }
        
        $amount = $row['total'];
        $order_id = 'ORDER_' . time() . '_' . $cust_id;
        
        // Store order details in session for verification
        $_SESSION['temp_order'] = [
            'order_id' => $order_id,
            'amount' => $amount
        ];
        
        echo json_encode([
            'success' => true,
            'order_id' => $order_id,
            'amount' => $amount * 100, // Convert to paisa
            'currency' => 'INR'
        ]);
        
    } catch(Exception $e) {
        error_log("Create Order Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit();
}

if(isset($_POST['process_payment'])) {
    try {
        $cust_id = $_SESSION['cust_id'];
        
        if(!isset($_SESSION['temp_order'])) {
            throw new Exception('Invalid order session');
        }
        
        $order_id = $_SESSION['temp_order']['order_id'];
        $amount = $_SESSION['temp_order']['amount'];
        
        $conn->begin_transaction();
        
        // Create order record
        $status = 'completed';
        $order_query = "INSERT INTO orders (cust_id, order_date, payment_id, total_amount, status) VALUES (?, NOW(), ?, ?, ?)";
        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("isds", $cust_id, $order_id, $amount, $status);
        $stmt->execute();
        $new_order_id = $conn->insert_id;
        
        // Get cart items
        $cart_query = "SELECT * FROM cart WHERE cust_id = ?";
        $stmt = $conn->prepare($cart_query);
        $stmt->bind_param("i", $cust_id);
        $stmt->execute();
        $cart_result = $stmt->get_result();
        
        // Create order items
        while($item = $cart_result->fetch_assoc()) {
            $item_query = "INSERT INTO order_items (order_id, crop_name, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($item_query);
            $stmt->bind_param("isdd", $new_order_id, $item['cropname'], $item['quantity'], $item['price']);
            $stmt->execute();
            
            // Update stock
            $update_stock = "UPDATE production_approx SET quantity = quantity - ? WHERE crop = ?";
            $stmt = $conn->prepare($update_stock);
            $stmt->bind_param("ds", $item['quantity'], $item['cropname']);
            $stmt->execute();
        }
        
        // Clear cart
        $clear_cart = "DELETE FROM cart WHERE cust_id = ?";
        $stmt = $conn->prepare($clear_cart);
        $stmt->bind_param("i", $cust_id);
        $stmt->execute();
        
        // Clear temp order from session
        unset($_SESSION['temp_order']);
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully',
            'order_id' => $new_order_id
        ]);
        
    } catch(Exception $e) {
        $conn->rollback();
        error_log("Process Payment Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit();
}

// If no valid action is specified
echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit();
