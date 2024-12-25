<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('../Includes/db.php');
require_once('../Includes/auth_check.php');

// Initialize variables
$customer_name = isset($_SESSION['customer_login_name']) ? $_SESSION['customer_login_name'] : '';

// Check if user is logged in
if (!isset($_SESSION['customer_login_user'])) {
    header("Location: ../index.php");
    exit();
}

// Debug logging
error_reporting(E_ALL);
ini_set('display_errors', 1); // Enable error display for debugging
error_log("Session data: " . print_r($_SESSION, true)); // Log session data

// Function to send JSON response
function sendJsonResponse($status, $message, $data = null) {
    header('Content-Type: application/json');
    $response = ['status' => $status, 'message' => $message];
    if ($data) {
        $response = array_merge($response, $data);
    }
    echo json_encode($response);
    exit;
}

// Handle AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    try {
        // Detailed logging of POST data
        error_log("POST Data Received: " . print_r($_POST, true));
        error_log("Session Data: " . print_r($_SESSION, true));

        // Validate input with more detailed checks
        $errors = [];
        if (empty($_POST['name'])) $errors[] = "Name is required";
        if (empty($_POST['phone'])) $errors[] = "Phone number is required";
        if (empty($_POST['address'])) $errors[] = "Address is required";
        if (empty($_POST['payment_method'])) $errors[] = "Payment method is required";
        
        if (!empty($errors)) {
            error_log("Validation Errors: " . implode(", ", $errors));
            sendJsonResponse('error', 'Validation failed: ' . implode(", ", $errors));
        }

        if (!isset($_SESSION['cust_id'])) {
            error_log("Customer ID not found in session");
            sendJsonResponse('error', 'Customer session expired. Please login again.');
        }

        // Start transaction
        mysqli_begin_transaction($connection);

        try {
            // Generate order ID
            $order_id = uniqid('ORD');
            $customer_id = $_SESSION['cust_id'];
            $total_amount = 0;
            $order_date = date('Y-m-d H:i:s');
            $payment_method = $_POST['payment_method'];
            $delivery_address = $_POST['address'];
            $phone = $_POST['phone'];

            // Get cart items from database with correct farmer ID
            $cart_query = "SELECT c.*, cr.Crop_name, f.farmer_id, f.email as farmer_email, f.farmer_name, fct.Trade_crop, fct.farmer_fkid, fct.costperkg as price
                          FROM cart c 
                          JOIN crops cr ON c.crop_id = cr.Crop_id
                          JOIN farmerlogin f ON c.farmer_id = f.farmer_id
                          JOIN farmer_crops_trade fct ON f.farmer_id = fct.farmer_fkid 
                          AND LOWER(TRIM(fct.Trade_crop)) = LOWER(TRIM(cr.Crop_name))
                          WHERE c.cust_id = ?";
            
            $cart_stmt = $connection->prepare($cart_query);
            $cart_stmt->bind_param("i", $customer_id);
            $cart_stmt->execute();
            $cart_result = $cart_stmt->get_result();
            
            if($cart_result->num_rows > 0) {
                $cart_items = [];
                // Calculate total amount with detailed logging
                while($item = $cart_result->fetch_assoc()) {
                    $cart_items[] = $item; // Store items for later use
                    $total_amount += $item['quantity'] * $item['price'];
                    error_log("Item in cart: " . json_encode($item));
                }
                error_log("Total Order Amount: " . $total_amount);

                // Prepare order insertion with comprehensive error checking
                $order_query = "INSERT INTO orders (order_id, customer_id, total_amount, order_date, payment_method, delivery_address, phone, status) 
                               VALUES (?, ?, ?, NOW(), ?, ?, ?, 'pending')";
                
                $stmt = mysqli_prepare($connection, $order_query);
                if ($stmt === false) {
                    $prepare_error = mysqli_error($connection);
                    error_log("Order Prepare Error: " . $prepare_error);
                    throw new Exception("Failed to prepare order query: " . $prepare_error);
                }

                error_log("Binding parameters - Order ID: " . $order_id . 
                         ", Customer ID: " . $customer_id . 
                         ", Total Amount: " . $total_amount . 
                         ", Payment Method: " . $payment_method . 
                         ", Address: " . $delivery_address . 
                         ", Phone: " . $phone);

                // Bind parameters with type checking - removed order_date since we're using NOW()
                mysqli_stmt_bind_param($stmt, "sidsss", 
                    $order_id,
                    $customer_id,
                    $total_amount,
                    $payment_method,
                    $delivery_address,
                    $phone
                );

                if (!mysqli_stmt_execute($stmt)) {
                    $execute_error = mysqli_stmt_error($stmt);
                    error_log("Order Execution Error: " . $execute_error);
                    throw new Exception("Failed to create order: " . $execute_error);
                }

                mysqli_stmt_close($stmt);

                // Insert into order history using stored cart items
                foreach($cart_items as $item) {
                    error_log("Processing item for order history: " . json_encode($item));
                    
                    $history_query = "INSERT INTO order_history (order_id, customer_id, farmer_id, crop_name, quantity, price, total_price, order_date) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                    $history_stmt = $connection->prepare($history_query);
                    $item_total = $item['quantity'] * $item['price'];
                    
                    // Log the values being inserted
                    error_log("Order History Values - Order ID: " . $order_id . 
                             ", Customer ID: " . $customer_id . 
                             ", Farmer ID: " . $item['farmer_id'] . 
                             ", Farmer Email: " . $item['farmer_email'] . 
                             ", Crop: " . $item['Crop_name'] . 
                             ", Quantity: " . $item['quantity'] . 
                             ", Price: " . $item['price'] . 
                             ", Total: " . $item_total);
                    
                    $history_stmt->bind_param("siisddd", 
                        $order_id,
                        $customer_id,
                        $item['farmer_id'],
                        $item['Crop_name'],
                        $item['quantity'],
                        $item['price'],
                        $item_total
                    );
                    
                    if(!$history_stmt->execute()) {
                        $error = $history_stmt->error;
                        error_log("Failed to insert order history: " . $error);
                        throw new Exception("Failed to record order history: " . $error);
                    }
                    
                    // Update crop stock
                    $update_trade_query = "UPDATE farmer_crops_trade 
                                         SET Crop_quantity = Crop_quantity - ? 
                                         WHERE farmer_fkid = ? 
                                         AND LOWER(TRIM(Trade_crop)) = LOWER(TRIM(?))";
                    $update_trade_stmt = $connection->prepare($update_trade_query);
                    $update_trade_stmt->bind_param("dis", 
                        $item['quantity'],
                        $item['farmer_fkid'],
                        $item['Trade_crop']
                    );
                    
                    if(!$update_trade_stmt->execute()) {
                        throw new Exception("Failed to update stock quantity");
                    }
                }
                
                // Clear cart after successful order
                $clear_cart_query = "DELETE FROM cart WHERE cust_id = ?";
                $clear_cart_stmt = $connection->prepare($clear_cart_query);
                $clear_cart_stmt->bind_param("i", $customer_id);
                
                if(!$clear_cart_stmt->execute()) {
                    throw new Exception("Failed to clear cart");
                }
                
                // Commit transaction
                mysqli_commit($connection);
                sendJsonResponse('success', 'Order placed successfully!', ['order_id' => $order_id]);
                
            } else {
                sendJsonResponse('error', 'Your cart is empty');
            }
        } catch (Exception $e) {
            mysqli_rollback($connection);
            error_log("Order Processing Error: " . $e->getMessage());
            sendJsonResponse('error', $e->getMessage());
        }
    } catch (Exception $e) {
        error_log("Order Processing Error (General): " . $e->getMessage());
        sendJsonResponse('error', $e->getMessage());
    }
}

// Regular page load - HTML output follows
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FarmersGateway</title>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* Navbar Styles */
        .navbar {
            background: #1a1a1a !important;
            padding: 0.5rem 1.5rem;
            height: 60px;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: white !important;
        }

        .nav-link {
            color: #ffffff !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #28a745 !important;
        }

        .dropdown-menu {
            background: #1a1a1a;
            border: 1px solid #333;
        }

        .dropdown-item {
            color: #ffffff;
        }

        .dropdown-item:hover {
            background: #333;
            color: #28a745;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: #1a1a1a;
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                padding: 1rem;
                z-index: 1000;
            }
        }

        /* Existing styles for checkout */
        body {
            padding-top: 60px;
        }
        
        :root {
            --primary-green: #2ecc71;
            --hover-green: #27ae60;
            --bg-light: #f8f9fa;
            --text-dark: #2c3e50;
            --border-color: #e0e0e0;
        }

        body {
            background-color: #f5f5f5;
            color: var(--text-dark);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .checkout-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .section-title {
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--primary-green);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: 6px;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.1);
            outline: none;
        }

        .payment-options {
            display: flex;
            gap: 2rem;
            margin-top: 0.5rem;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .payment-option input[type="radio"] {
            width: 1.2rem;
            height: 1.2rem;
            margin: 0;
        }

        .payment-option label {
            margin: 0;
            font-weight: normal;
        }

        .order-summary {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 2rem;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 1.5rem;
        }

        .summary-table th {
            color: var(--text-dark);
            font-weight: 600;
            padding: 0.75rem;
            text-align: left;
            border-bottom: 2px solid var(--border-color);
        }

        .summary-table td {
            padding: 1rem 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-table tr:last-child td {
            border-bottom: none;
        }

        .total-row {
            font-weight: 600;
            font-size: 1.1rem;
            border-top: 2px solid var(--border-color) !important;
        }

        .btn-place-order {
            width: 100%;
            padding: 1rem;
            background-color: var(--primary-green);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.2s;
        }

        .btn-place-order:hover {
            background-color: var(--hover-green);
            transform: translateY(-1px);
        }

        .btn-place-order:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .checkout-container {
                padding: 0 0.5rem;
            }
            
            .checkout-section {
                padding: 1.5rem;
            }
            
            .payment-options {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-store text-success"></i>
                <span class="text-success">FARMERSGATEWAY</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="cbuy_crops.php">
                            <i class="fas fa-shopping-cart"></i>
                            Buy Crops
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link px-3" href="cpurchase_history.php">
                            <i class="fas fa-history"></i>
                            Purchase History
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-tools"></i>
                            Tools
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="cfarmtube.php">
                                    <i class="fas fa-video"></i>
                                    FarmTube
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link px-3" href="cprofile.php">
                            <i class="fas fa-user"></i>
                            <?php echo $customer_name; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link px-3" href="clogout.php">
                            <i class="fas fa-power-off"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="checkout-container">
        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-section">
                    <h2 class="section-title">Delivery Details</h2>
                    <form id="checkoutForm">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Delivery Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Payment Method</label>
                            <div class="payment-options">
                                <div class="payment-option">
                                    <input type="radio" id="cod" name="payment_method" value="cod" checked>
                                    <label for="cod">Cash on Delivery</label>
                                </div>
                                <div class="payment-option">
                                    <input type="radio" id="online" name="payment_method" value="online" disabled>
                                    <label for="online" style="color: #999;">Online Payment (Coming Soon)</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="checkout-section">
                    <h2 class="section-title">Order Summary</h2>
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            $cart_query = "SELECT c.*, cr.Crop_name, f.farmer_id, f.email as farmer_email, f.farmer_name, fct.Trade_crop, fct.farmer_fkid, fct.costperkg as price
                                          FROM cart c 
                                          JOIN crops cr ON c.crop_id = cr.Crop_id
                                          JOIN farmerlogin f ON c.farmer_id = f.farmer_id
                                          JOIN farmer_crops_trade fct ON f.farmer_id = fct.farmer_fkid 
                                          AND LOWER(TRIM(fct.Trade_crop)) = LOWER(TRIM(cr.Crop_name))
                                          WHERE c.cust_id = ?";
                            $cart_stmt = $connection->prepare($cart_query);
                            $cart_stmt->bind_param("i", $_SESSION['cust_id']);
                            $cart_stmt->execute();
                            $cart_result = $cart_stmt->get_result();
                            if($cart_result->num_rows > 0) {
                                while($item = $cart_result->fetch_assoc()) {
                                    $total += $item['quantity'] * $item['price'];
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item["Crop_name"]); ?></td>
                                    <td class="text-center"><?php echo number_format($item["quantity"], 2); ?></td>
                                    <td class="text-right">₹<?php echo number_format($item["quantity"] * $item["price"], 2); ?></td>
                                </tr>
                            <?php
                                }
                            }
                            ?>
                            <tr class="total-row">
                                <td colspan="2">Total Amount:</td>
                                <td class="text-right">₹<?php echo number_format($total, 2); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="submit" form="checkoutForm" class="btn-place-order">
                        <i class="fas fa-lock mr-2"></i>Place Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        function validateForm() {
            let isValid = true;
            const name = $('#name').val().trim();
            const phone = $('#phone').val().trim();
            const address = $('#address').val().trim();
            const payment_method = $('input[name="payment_method"]:checked').val();
            
            // Reset previous error states
            $('.form-control').removeClass('is-invalid');
            $('.payment-options').removeClass('is-invalid');
            
            if (!name) {
                $('#name').addClass('is-invalid');
                isValid = false;
            }
            
            if (!phone || !/^\d{10}$/.test(phone)) {
                $('#phone').addClass('is-invalid');
                isValid = false;
            }
            
            if (!address) {
                $('#address').addClass('is-invalid');
                isValid = false;
            }
            
            if (!payment_method) {
                $('.payment-options').addClass('is-invalid');
                isValid = false;
            }
            
            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fill in all required fields correctly.',
                    confirmButtonColor: '#2ecc71'
                });
            }
            
            return isValid;
        }

        $('#checkoutForm').on('submit', function(e) {
            e.preventDefault();
            
            // Validate form
            if (!validateForm()) {
                return false;
            }
            
            // Get form data
            const formData = {
                name: $('#name').val().trim(),
                phone: $('#phone').val().trim(),
                address: $('#address').val().trim(),
                payment_method: $('input[name="payment_method"]:checked').val()
            };
            
            // Show loading state
            Swal.fire({
                title: 'Processing Order',
                text: 'Please wait...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit form
            $.ajax({
                type: 'POST',
                url: window.location.href,
                data: formData,
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    console.log('Response:', response);
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Order Placed Successfully!',
                            text: 'Your order has been placed successfully.',
                            confirmButtonColor: '#2ecc71'
                        }).then((result) => {
                            window.location.href = 'cpurchase_history.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Order Processing Failed',
                            text: response.message || 'There was an error processing your order. Please try again.',
                            confirmButtonColor: '#2ecc71'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);
                    
                    let errorMessage = 'There was an unexpected error processing your order.';
                    try {
                        // Try to parse JSON response
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch (e) {
                        // If response is not JSON, use the raw response text
                        errorMessage = xhr.responseText || errorMessage;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Order Processing Failed',
                        text: errorMessage,
                        confirmButtonColor: '#2ecc71'
                    });
                }
            });
        });
        
        // Remove invalid state on input
        $('.form-control').on('input', function() {
            $(this).removeClass('is-invalid');
        });
        
        $('input[name="payment_method"]').on('change', function() {
            $('.payment-options').removeClass('is-invalid');
        });
    });
    </script>
</body>
</html>
