<?php
include ('csession.php');
include ('../sql.php');
include ('cart_init.php');

if(!isset($_SESSION['customer_login_user'])){
    header("location: ../index.php");
}

// Initialize cart
initializeCart($conn);

if(!isset($_GET['farmer_id'])) {
    header("location: cbuy_crops.php");
    exit;
}

$farmer_id = $_GET['farmer_id'];

// Get farmer details
$farmer_query = "SELECT * FROM farmerlogin WHERE farmer_id = ?";
$stmt = $conn->prepare($farmer_query);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$farmer_result = $stmt->get_result();
$farmer = $farmer_result->fetch_assoc();

if(!$farmer) {
    header("location: cbuy_crops.php");
    exit;
}

// Handle add to cart action
if(isset($_POST['add_to_cart'])) {
    try {
        $crop_name = trim($_POST['crop_name']);
        $quantity = floatval($_POST['quantity']);
        $price = floatval($_POST['price']);
        
        if(empty($crop_name) || $quantity <= 0) {
            throw new Exception("Invalid input data");
        }

        // Start transaction
        mysqli_begin_transaction($conn);
        
        // Check if crop exists in crops table
        $crop_query = "SELECT Crop_id FROM crops WHERE LOWER(TRIM(Crop_name)) = LOWER(?)";
        $stmt = $conn->prepare($crop_query);
        $stmt->bind_param("s", $crop_name);
        $stmt->execute();
        $crop_result = $stmt->get_result();
        
        if($crop_result->num_rows == 0) {
            // Add crop if it doesn't exist
            $insert_crop = "INSERT INTO crops (Crop_name) VALUES (?)";
            $stmt = $conn->prepare($insert_crop);
            $stmt->bind_param("s", $crop_name);
            if(!$stmt->execute()) {
                // If insert fails due to race condition, try to get the existing crop_id
                $stmt = $conn->prepare($crop_query);
                $stmt->bind_param("s", $crop_name);
                $stmt->execute();
                $crop_result = $stmt->get_result();
                if($crop_result->num_rows == 0) {
                    throw new Exception("Failed to get crop ID. Please try again.");
                }
                $crop_row = $crop_result->fetch_assoc();
                $crop_id = $crop_row['Crop_id'];
            } else {
                $crop_id = $conn->insert_id;
            }
        } else {
            $crop_row = $crop_result->fetch_assoc();
            $crop_id = $crop_row['Crop_id'];
        }
        
        if(!$crop_id) {
            throw new Exception("Invalid crop ID. Please try again.");
        }
        
        // Check stock availability
        $stock_query = "SELECT Crop_quantity 
                       FROM farmer_crops_trade 
                       WHERE farmer_fkid = ? 
                       AND LOWER(TRIM(Trade_crop)) = LOWER(TRIM(?))
                       AND Crop_quantity > 0
                       FOR UPDATE";
        $stmt = $conn->prepare($stock_query);
        $stmt->bind_param("is", $farmer_id, $crop_name);
        $stmt->execute();
        $stock_result = $stmt->get_result();

        if($stock_result->num_rows == 0) {
            throw new Exception("Crop not available from this farmer");
        }

        $stock_row = $stock_result->fetch_assoc();
        $available_quantity = floatval($stock_row['Crop_quantity']);

        if($available_quantity <= 0) {
            throw new Exception("Crop is out of stock");
        }

        if($quantity > $available_quantity) {
            throw new Exception("Cannot add {$quantity} kg to cart. Available stock: {$available_quantity} kg");
        }

        // Check if item already exists in cart
        $cart_check = "SELECT id, quantity FROM cart 
                      WHERE cust_id = ? AND farmer_id = ? AND crop_id = ?
                      FOR UPDATE";
        $stmt = $conn->prepare($cart_check);
        $stmt->bind_param("iii", $_SESSION['cust_id'], $farmer_id, $crop_id);
        $stmt->execute();
        $cart_result = $stmt->get_result();

        if($cart_result->num_rows > 0) {
            // Update existing cart item
            $cart_row = $cart_result->fetch_assoc();
            $new_quantity = $cart_row['quantity'] + $quantity;
            
            if($new_quantity > $available_quantity) {
                throw new Exception("Total quantity ({$new_quantity} kg) exceeds available stock ({$available_quantity} kg)");
            }
            
            $update_cart = "UPDATE cart SET quantity = ?, price = ? WHERE id = ?";
            $stmt = $conn->prepare($update_cart);
            $stmt->bind_param("ddi", $new_quantity, $price, $cart_row['id']);
            $stmt->execute();
        } else {
            // Add new cart item
            $insert_cart = "INSERT INTO cart (cust_id, farmer_id, crop_id, quantity, price) 
                           VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_cart);
            $stmt->bind_param("iiidi", $_SESSION['cust_id'], $farmer_id, $crop_id, $quantity, $price);
            if (!$stmt->execute()) {
                // If insert fails due to duplicate, try to update
                if ($conn->errno == 1062) { // Duplicate entry error
                    $update_cart = "UPDATE cart 
                                  SET quantity = quantity + ?, price = ? 
                                  WHERE cust_id = ? AND farmer_id = ? AND crop_id = ?";
                    $stmt = $conn->prepare($update_cart);
                    $stmt->bind_param("ddiii", $quantity, $price, $_SESSION['cust_id'], $farmer_id, $crop_id);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to update cart. Please try again.");
                    }
                } else {
                    throw new Exception("Failed to add item to cart: " . $conn->error);
                }
            }
        }

        // Update stock
        $update_stock = "UPDATE farmer_crops_trade 
                        SET Crop_quantity = Crop_quantity - ? 
                        WHERE farmer_fkid = ? 
                        AND LOWER(TRIM(Trade_crop)) = LOWER(TRIM(?))
                        AND Crop_quantity >= ?";
        $stmt = $conn->prepare($update_stock);
        $stmt->bind_param("diss", $quantity, $farmer_id, $crop_name, $quantity);

        if(!$stmt->execute() || $stmt->affected_rows == 0) {
            throw new Exception("Failed to update stock. Please try again.");
        }
        
        mysqli_commit($conn);
        $_SESSION['success_message'] = "Item added to cart successfully";
        
    } catch(Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error_message'] = $e->getMessage();
    }
    
    header("Location: farmer_crops.php?farmer_id=" . $farmer_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo htmlspecialchars($farmer['farmer_name']); ?>'s Crops - Agriculture Portal</title>
    <?php include('cheader.php'); ?>
    <style>
        .farmer-profile {
            background: linear-gradient(135deg, #2dce89 0%, #2dcecc 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .farmer-profile h2 {
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .farmer-info {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .info-item i {
            font-size: 1.2rem;
        }
        .crop-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            height: 100%;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .crop-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .crop-header {
            background: #f8f9fe;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .crop-body {
            padding: 1.5rem;
        }
        .crop-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #32325d;
            margin-bottom: 0.5rem;
        }
        .price-tag {
            display: inline-block;
            background: #2dce89;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
        }
        .msp-tag {
            display: inline-block;
            background: #f7fafc;
            color: #8898aa;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            margin-left: 0.5rem;
        }
        .stock-info {
            margin: 1rem 0;
            padding: 0.75rem;
            border-radius: 10px;
            background: #f6f9fc;
        }
        .quantity-input {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem;
            width: 100%;
            margin: 1rem 0;
            transition: all 0.2s;
        }
        .quantity-input:focus {
            border-color: #2dce89;
            box-shadow: 0 0 0 0.2rem rgba(45, 206, 137, 0.25);
        }
        .btn-add-cart {
            background: #2dce89;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
            width: 100%;
            margin-top: 1rem;
        }
        .btn-add-cart:hover {
            background: #26af74;
            transform: translateY(-2px);
        }
        .btn-add-cart:disabled {
            background: #e9ecef;
            cursor: not-allowed;
            transform: none;
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <?php include ('cnav.php'); ?>

    <div class="container py-5">
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

        <div class="farmer-profile fade-in">
            <h2><i class="fas fa-user-circle mr-2"></i>Farmer Details</h2>
            <div class="farmer-info">
                <div class="info-item">
                    <i class="fas fa-user"></i>
                    <span><?php echo htmlspecialchars($farmer['farmer_name']); ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($farmer['F_Location']); ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <span><?php echo htmlspecialchars($farmer['phone_no']); ?></span>
                </div>
            </div>
        </div>

        <div class="row">
            <?php
            $crops_query = "SELECT 
                               fct.Trade_crop,
                               MIN(fct.costperkg) as costperkg,
                               fct.msp,
                               SUM(fct.Crop_quantity) as Crop_quantity,
                               c.Crop_name
                           FROM farmer_crops_trade fct
                           LEFT JOIN crops c ON LOWER(TRIM(fct.Trade_crop)) = LOWER(TRIM(c.Crop_name))
                           WHERE fct.farmer_fkid = ? AND fct.Crop_quantity > 0
                           GROUP BY fct.Trade_crop, fct.msp
                           ORDER BY costperkg ASC";
            $stmt = $conn->prepare($crops_query);
            $stmt->bind_param("i", $farmer_id);
            $stmt->execute();
            $crops_result = $stmt->get_result();
            
            if($crops_result->num_rows > 0):
                while($crop = $crops_result->fetch_assoc()):
                    $available_quantity = max(0, floatval($crop['Crop_quantity']));
            ?>
                <div class="col-md-6 col-lg-4 mb-4 fade-in">
                    <div class="crop-card">
                        <div class="crop-header">
                            <h3 class="crop-name"><?php echo htmlspecialchars($crop['Trade_crop']); ?></h3>
                            <div>
                                <span class="price-tag">₹<?php echo htmlspecialchars($crop['costperkg']); ?>/kg</span>
                                <span class="msp-tag">MSP: ₹<?php echo htmlspecialchars($crop['msp']); ?></span>
                            </div>
                        </div>
                        <div class="crop-body">
                            <div class="stock-info">
                                <i class="fas fa-cubes mr-2"></i>
                                Available Stock: <?php echo number_format($available_quantity, 2); ?> kg
                            </div>
                            
                            <?php if($available_quantity > 0): ?>
                                <form method="post" action="" class="add-to-cart-form">
                                    <input type="hidden" name="crop_name" value="<?php echo htmlspecialchars($crop['Trade_crop']); ?>">
                                    <input type="hidden" name="price" value="<?php echo htmlspecialchars($crop['costperkg']); ?>">
                                    <div class="form-group">
                                        <input type="number" name="quantity" class="quantity-input" 
                                               min="1" max="<?php echo $available_quantity; ?>" 
                                               placeholder="Enter quantity (kg)" required>
                                    </div>
                                    <button type="submit" name="add_to_cart" class="btn-add-cart">
                                        <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn-add-cart" disabled>
                                    <i class="fas fa-times-circle mr-2"></i>Out of Stock
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile;
            else:
            ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h3 class="text-muted">No crops available</h3>
                    <p class="text-muted">This farmer currently has no crops listed for sale</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="order-summary mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <?php
                    $cart_query = "SELECT c.*, cr.Crop_name, f.farmer_name 
                                 FROM cart c 
                                 JOIN crops cr ON c.crop_id = cr.Crop_id 
                                 JOIN farmerlogin f ON c.farmer_id = f.farmer_id
                                 WHERE c.cust_id = ? AND c.farmer_id = ?";
                    $cart_stmt = $conn->prepare($cart_query);
                    $cart_stmt->bind_param("ii", $_SESSION['cust_id'], $farmer_id);
                    $cart_stmt->execute();
                    $cart_result = $cart_stmt->get_result();
                    
                    if($cart_result->num_rows > 0) {
                        $total_amount = 0;
                        ?>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while($item = $cart_result->fetch_assoc()) {
                                        $item_total = $item['quantity'] * $item['price'];
                                        $total_amount += $item_total;
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['Crop_name']); ?></td>
                                            <td><?php echo number_format($item['quantity'], 2); ?> kg</td>
                                            <td>₹<?php echo number_format($item_total, 2); ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right border-top"><strong>Total Amount:</strong></td>
                                        <td class="border-top"><strong>₹<?php echo number_format($total_amount, 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">No items in cart from this farmer</p>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="text-center mt-3">
                        <a href="http://localhost/agriculture-portal/customer/view_cart.php" class="btn btn-success btn-block">
                            <i class="fas fa-shopping-cart mr-2"></i>View Full Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("../modern-footer.php"); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
