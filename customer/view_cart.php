<?php
include ('csession.php');
include ('../sql.php');
include ('cart_init.php');

if(!isset($_SESSION['customer_login_user'])){
    header("location: ../index.php");
}

// Initialize cart
initializeCart($conn);

// Handle cart item removal
if(isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
    try {
        $item_id = intval($_GET["id"]);
        
        mysqli_begin_transaction($conn);
        
        // Get cart item details before deletion
        $select_query = "SELECT c.*, cr.Crop_name 
                        FROM cart c 
                        JOIN crops cr ON c.crop_id = cr.Crop_id
                        WHERE c.id = ? AND c.cust_id = ?
                        FOR UPDATE";
        $stmt = $conn->prepare($select_query);
        $stmt->bind_param("ii", $item_id, $_SESSION['cust_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        
        if(!$item) {
            throw new Exception('Cart item not found');
        }
        
        // Delete from cart
        $delete_query = "DELETE FROM cart WHERE id = ? AND cust_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("ii", $item_id, $_SESSION['cust_id']);
        
        if(!$stmt->execute()) {
            throw new Exception('Failed to remove item from cart');
        }
        
        // Return quantity to stock
        $update_query = "UPDATE farmer_crops_trade 
                        SET Crop_quantity = Crop_quantity + ? 
                        WHERE farmer_fkid = ? 
                        AND LOWER(TRIM(Trade_crop)) = LOWER(TRIM(?))";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("dis", $item['quantity'], $item['farmer_id'], $item['Crop_name']);
        
        if(!$stmt->execute()) {
            throw new Exception('Failed to update stock');
        }
        
        mysqli_commit($conn);
        $_SESSION['success_message'] = "Item removed from cart successfully";
        
    } catch(Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = $e->getMessage();
    }
    
    header("Location: view_cart.php");
    exit;
}

// Get cart items
$cart_query = "SELECT c.id, cr.Crop_name, f.farmer_name, f.F_Location as farmer_location,
                      c.quantity, c.price, fct.Crop_quantity as stock_quantity, 
                      fct.costperkg, fct.msp,
                      (c.quantity * c.price) as item_total
               FROM cart c
               JOIN crops cr ON c.crop_id = cr.Crop_id
               JOIN farmerlogin f ON c.farmer_id = f.farmer_id
               LEFT JOIN farmer_crops_trade fct ON c.farmer_id = fct.farmer_fkid 
               AND LOWER(TRIM(fct.Trade_crop)) = LOWER(TRIM(cr.Crop_name))
               WHERE c.cust_id = ?
               GROUP BY c.farmer_id, c.crop_id
               ORDER BY c.created_at DESC";

$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $_SESSION['cust_id']);
$stmt->execute();
$cart_result = $stmt->get_result();

// Calculate cart total
$cart_total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Cart - Agriculture Portal</title>
    <?php include('cheader.php'); ?>
    <style>
        .cart-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        .cart-item {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            transition: transform 0.2s;
        }
        .cart-item:hover {
            transform: translateY(-3px);
        }
        .cart-header {
            background: #2dce89;
            color: white;
            padding: 1rem;
            border-radius: 10px 10px 0 0;
            margin-bottom: 2rem;
        }
        .cart-total {
            background: #f8f9fe;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .remove-btn {
            color: #f5365c;
            border: 1px solid #f5365c;
            background: transparent;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.2s;
        }
        .remove-btn:hover {
            background: #f5365c;
            color: white;
        }
        .checkout-btn {
            background: #2dce89;
            border: none;
            padding: 1rem 2rem;
            border-radius: 5px;
            color: white;
            font-weight: 600;
            transition: all 0.2s;
        }
        .checkout-btn:hover {
            background: #26af74;
            transform: translateY(-2px);
        }
        .price-tag {
            color: #2dce89;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .msp-tag {
            color: #8898aa;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <?php include ('cnav.php'); ?>

    <div class="container-fluid py-5">
        <div class="cart-container">
            <div class="cart-header">
                <h2 class="mb-0">My Shopping Cart</h2>
            </div>

            <?php if(isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php elseif(isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if($cart_result && $cart_result->num_rows > 0): ?>
                <?php while($item = $cart_result->fetch_assoc()): 
                    $cart_total += $item['item_total'];
                ?>
                    <div class="cart-item">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <h5 class="mb-1"><?php echo htmlspecialchars($item['Crop_name']); ?></h5>
                                    <small class="text-muted">
                                        Seller: <?php echo htmlspecialchars($item['farmer_name']); ?>
                                        <br>
                                        Location: <?php echo htmlspecialchars($item['farmer_location']); ?>
                                    </small>
                                </div>
                                <div class="col-md-2">
                                    <div class="price-tag">₹<?php echo htmlspecialchars($item['price']); ?>/kg</div>
                                    <div class="msp-tag">MSP: ₹<?php echo htmlspecialchars($item['msp'] ?? 'N/A'); ?></div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <div class="font-weight-bold"><?php echo htmlspecialchars($item['quantity']); ?> kg</div>
                                    <small class="text-muted">Quantity</small>
                                </div>
                                <div class="col-md-2 text-center">
                                    <div class="price-tag">₹<?php echo htmlspecialchars($item['item_total']); ?></div>
                                    <small class="text-muted">Subtotal</small>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a href="?action=delete&id=<?php echo $item['id']; ?>" 
                                       class="remove-btn"
                                       onclick="return confirm('Are you sure you want to remove this item?');">
                                        <i class="fas fa-trash-alt mr-1"></i> Remove
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

                <div class="cart-total mt-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="mb-0">Cart Total</h4>
                        </div>
                        <div class="col-md-6 text-right">
                            <h3 class="price-tag mb-0">₹<?php echo number_format($cart_total, 2); ?></h3>
                        </div>
                    </div>
                </div>

                <div class="text-right mt-4">
                    <a href="cbuy_crops.php" class="btn btn-outline-primary mr-2">
                        <i class="fas fa-arrow-left mr-1"></i> Continue Shopping
                    </a>
                    <a href="checkout.php" class="checkout-btn">
                        <i class="fas fa-shopping-cart mr-1"></i> Proceed to Checkout
                    </a>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h3 class="text-muted">Your cart is empty</h3>
                    <p class="text-muted mb-4">Add some items to your cart and they will appear here</p>
                    <a href="cbuy_crops.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left mr-1"></i> Continue Shopping
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include("../modern-footer.php"); ?>
</body>
</html>
