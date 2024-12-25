<?php
session_start();
require_once('../Includes/db.php');
require_once('../Includes/auth_check.php');

// Check if user is logged in
if (!isset($_SESSION['customer_login_user'])) {
    header("Location: ../login.php");
    exit();
}

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    header("Location: cbuy_crops.php");
    exit();
}

$order_id = mysqli_real_escape_string($connection, $_GET['order_id']);
$order = null;
$order_items = [];

try {
    // Get order details
    $order_query = "SELECT o.*, c.cust_name as customer_name, c.email 
                    FROM orders o 
                    JOIN custlogin c ON o.customer_id = c.cust_id 
                    WHERE o.order_id = ?";
    
    $stmt = mysqli_prepare($connection, $order_query);
    if ($stmt === false) {
        throw new Exception("Failed to prepare order query: " . mysqli_error($connection));
    }
    
    if (!mysqli_stmt_bind_param($stmt, "s", $order_id)) {
        throw new Exception("Failed to bind order parameters: " . mysqli_stmt_error($stmt));
    }
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to execute order query: " . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        throw new Exception("Failed to get order result: " . mysqli_stmt_error($stmt));
    }
    
    if ($row = mysqli_fetch_assoc($result)) {
        $order = $row;
        
        // Get order items
        $items_query = "SELECT * FROM order_items WHERE order_id = ?";
        $stmt_items = mysqli_prepare($connection, $items_query);
        
        if ($stmt_items === false) {
            throw new Exception("Failed to prepare items query: " . mysqli_error($connection));
        }
        
        if (!mysqli_stmt_bind_param($stmt_items, "s", $order_id)) {
            throw new Exception("Failed to bind items parameters: " . mysqli_stmt_error($stmt_items));
        }
        
        if (!mysqli_stmt_execute($stmt_items)) {
            throw new Exception("Failed to execute items query: " . mysqli_stmt_error($stmt_items));
        }
        
        $items_result = mysqli_stmt_get_result($stmt_items);
        if ($items_result === false) {
            throw new Exception("Failed to get items result: " . mysqli_stmt_error($stmt_items));
        }
        
        while ($item = mysqli_fetch_assoc($items_result)) {
            $order_items[] = $item;
        }
    } else {
        header("Location: cbuy_crops.php");
        exit();
    }
} catch (Exception $e) {
    error_log("Order confirmation error: " . $e->getMessage());
    header("Location: cbuy_crops.php?error=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Agriculture Portal</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet">
    
    <!-- Icons -->
    <link href="../assets/vendor/nucleo/css/nucleo.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" rel="stylesheet">
    
    <!-- Theme CSS -->
    <link type="text/css" href="../assets/css/argon.min.css" rel="stylesheet">
    <link type="text/css" href="../assets/css/style.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fe !important;
        }
        .section-shaped .shape {
            background: linear-gradient(150deg, #8965e0 15%, #2dce89 70%, #2dcecc 94%);
        }
        .card {
            border: 0;
            box-shadow: 0 0 2rem 0 rgba(136, 152, 170, .15);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
        }
        .order-info {
            background: #f6f9fc;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .info-title {
            color: #8898aa;
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        .info-value {
            color: #32325d;
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.875rem;
        }
        .status-pending {
            background: #ffd600;
            color: #fff;
        }
        .table thead th {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
        }
        .table td {
            font-size: 0.9rem;
            padding: 1rem;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
        }
        .btn-icon .btn-inner--icon {
            margin-right: 0.5rem;
        }
        .section-shaped {
            padding-top: 8rem;
            padding-bottom: 8rem;
            position: relative;
            overflow: hidden;
        }
        
        /* Navbar styles */
        .navbar {
            background-color: #172b4d !important;
            box-shadow: 0 0 2rem 0 rgba(136, 152, 170, .15);
        }
        
        .navbar-brand .text-success {
            font-weight: 600;
        }
        
        .nav-link {
            color: #fff !important;
            font-weight: 500;
            transition: all 0.15s ease;
        }
        
        .nav-link:hover {
            color: #2dce89 !important;
        }
        
        .navbar .dropdown-menu {
            border: 0;
            box-shadow: 0 50px 100px rgba(50,50,93,.1), 0 15px 35px rgba(50,50,93,.15), 0 5px 15px rgba(0,0,0,.1);
            border-radius: .375rem;
        }
        
        .dropdown-item {
            padding: .5rem 1rem;
            font-weight: 500;
        }
        
        .dropdown-item:hover {
            background-color: #f6f9fc;
        }
        
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: #172b4d;
                padding: 1.5rem;
                border-radius: 0.375rem;
                box-shadow: 0 50px 100px rgba(50,50,93,.1), 0 15px 35px rgba(50,50,93,.15), 0 5px 15px rgba(0,0,0,.1);
            }
        }
    </style>
</head>
<body>
    <?php include('cnav.php'); ?>
    
    <div class="main-content">
        <section class="section section-shaped section-lg">
            <div class="shape shape-style-1 bg-gradient-success">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <div class="container pt-lg-7">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card bg-secondary shadow border-0">
                            <div class="card-header bg-white px-lg-4 py-lg-4">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h3 class="mb-0">Order Confirmed</h3>
                                    </div>
                                    <div class="col-4 text-right">
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body px-lg-4 py-lg-4">
                                <div class="text-center mb-4">
                                    <h4 class="text-success">Thank you for your purchase!</h4>
                                    <p class="text-muted">We'll notify you once your order has been processed.</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label text-muted">ORDER ID</label>
                                            <p class="h5"><?php echo htmlspecialchars($order['order_id']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label text-muted">DATE</label>
                                            <p class="h5"><?php echo date('d M Y', strtotime($order['order_date'])); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label text-muted">STATUS</label>
                                            <p class="h5">
                                                <span class="badge badge-pill badge-warning"><?php echo htmlspecialchars($order['status']); ?></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label text-muted">PAYMENT METHOD</label>
                                            <p class="h5"><?php echo htmlspecialchars($order['payment_method']); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="form-group">
                                    <label class="form-control-label text-muted">DELIVERY DETAILS</label>
                                    <div class="pl-3">
                                        <p class="mb-1"><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></p>
                                        <p class="mb-1"><?php echo htmlspecialchars($order['delivery_address']); ?></p>
                                        <p class="mb-0">Phone: <?php echo htmlspecialchars($order['phone']); ?></p>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="table-responsive">
                                    <table class="table align-items-center">
                                        <thead>
                                            <tr>
                                                <th scope="col">Item</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col" class="text-right">Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($order_items as $item): ?>
                                            <tr>
                                                <td>
                                                    <span class="font-weight-bold"><?php echo htmlspecialchars($item['crop_name'] ?? 'Unknown Item'); ?></span>
                                                </td>
                                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                                <td class="text-right">₹<?php echo number_format($item['price'], 2); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <tr>
                                                <td colspan="2" class="text-right font-weight-bold">Total:</td>
                                                <td class="text-right font-weight-bold">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-center mt-4">
                                    <a href="cpurchase_history.php" class="btn btn-success">
                                        <i class="fas fa-history mr-2"></i>View Purchase History
                                    </a>
                                    <a href="cbuy_crops.php" class="btn btn-primary">
                                        <i class="fas fa-shopping-cart mr-2"></i>Continue Shopping
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include("../modern-footer.php"); ?>

    <!-- Core -->
    <script src="../assets/vendor/jquery/jquery.min.js"></script>
    <script src="../assets/vendor/popper/popper.min.js"></script>
    <script src="../assets/vendor/bootstrap/bootstrap.min.js"></script>
    <script src="../assets/vendor/headroom/headroom.min.js"></script>
    
    <!-- Theme JS -->
    <script src="../assets/js/argon.min.js"></script>
</body>
</html>
