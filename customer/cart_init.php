<?php
function initializeCart($conn) {
    if(!isset($_SESSION['customer_login_user'])) {
        return false;
    }
    
    // Create cart and crops tables if not exists
    $sql = file_get_contents(__DIR__ . '/cart_table.sql');
    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
    }
    
    // Get customer ID from session if not already set
    if(!isset($_SESSION['cust_id'])) {
        $query = "SELECT cust_id FROM custlogin WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $_SESSION['customer_login_user']);
        $stmt->execute();
        $result = $stmt->get_result();
        if($row = $result->fetch_assoc()) {
            $_SESSION['cust_id'] = $row['cust_id'];
        } else {
            return false;
        }
    }
    
    error_log("Initializing cart for customer ID: " . $_SESSION['cust_id']);
    
    // Initialize session shopping cart if not exists
    if(!isset($_SESSION["shopping_cart"])) {
        $_SESSION["shopping_cart"] = array();
        
        // Load existing cart items from database with farmer information
        $query = "SELECT c.*, cr.Crop_name as cropname, f.farmer_name, f.F_Location as farmer_location, 
                        fct.Crop_quantity as stock_quantity, fct.costperkg as price, fct.msp
                 FROM cart c 
                 JOIN crops cr ON c.crop_id = cr.Crop_id
                 JOIN farmerlogin f ON c.farmer_id = f.farmer_id
                 LEFT JOIN farmer_crops_trade fct ON c.farmer_id = fct.farmer_fkid 
                 AND LOWER(TRIM(fct.Trade_crop)) = LOWER(TRIM(cr.Crop_name))
                 WHERE c.cust_id = ?
                 ORDER BY c.created_at DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $_SESSION['cust_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result === false) {
            error_log("Cart initialization query failed: " . $conn->error);
            return false;
        }
        
        error_log("Found " . $result->num_rows . " items in cart");
        
        while($row = $result->fetch_assoc()) {
            $item = array(
                'id' => $row['id'],
                'crop_id' => $row['crop_id'],
                'farmer_id' => $row['farmer_id'],
                'quantity' => $row['quantity'],
                'price' => $row['price'],
                'cropname' => $row['cropname'],
                'farmer_name' => $row['farmer_name'],
                'farmer_location' => $row['farmer_location'],
                'stock_quantity' => $row['stock_quantity'],
                'msp' => $row['msp']
            );
            $_SESSION["shopping_cart"][] = $item;
        }
    }
    
    return true;
}
?>
