<?php
include ('fsession.php');
require_once("../sql.php");

if(!isset($_SESSION['farmer_login_user'])){
    header("location: ../index.php");
    exit();
} 

// Debug POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST Data: " . print_r($_POST, true));
}

// Get farmer ID from session
$user_check = $_SESSION['farmer_login_user'];
$query = "SELECT farmer_id, farmer_name FROM farmerlogin WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_check);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$farmer_id = $row['farmer_id'];
$farmer_name = $row['farmer_name'];

// Get pending orders with full details
$query = "SELECT oh.*, o.status, o.order_date, o.delivery_address, o.phone,
                 cl.cust_name, cl.phone_no as customer_phone
          FROM order_history oh
          LEFT JOIN orders o ON o.order_id = oh.order_id
          LEFT JOIN custlogin cl ON o.customer_id = cl.cust_id
          WHERE oh.farmer_id = ? AND o.status IN ('pending', 'processing')
          ORDER BY o.order_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$orders_result = $stmt->get_result();

// Store orders in array for display
$orders = array();
while ($row = $orders_result->fetch_assoc()) {
    $orders[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['action'])) {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];
    
    error_log("Processing order: " . $order_id . " with action: " . $action);
    
    try {
        // Begin transaction
        $conn->begin_transaction();
        
        // Set new status based on action
        switch($action) {
            case 'process':
                $new_status = 'processing';
                break;
            case 'complete':
                $new_status = 'completed';
                break;
            case 'cancel':
                $new_status = 'cancelled';
                break;
            default:
                throw new Exception("Invalid action");
        }
        
        error_log("Setting new status to: " . $new_status);
        
        // Update orders table
        $update_query = "UPDATE orders SET status = ? WHERE order_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ss", $new_status, $order_id);
        
        if (!$update_stmt->execute()) {
            error_log("Failed to update order status: " . $conn->error);
            throw new Exception("Failed to update order status");
        }
        
        // If cancelling, restore the crop quantity
        if ($action === 'cancel') {
            $restore_query = "UPDATE farmer_crops fc 
                            JOIN order_history oh ON oh.crop_name = fc.crop_name
                            SET fc.quantity = fc.quantity + oh.quantity
                            WHERE oh.order_id = ? AND oh.farmer_id = ?";
            $restore_stmt = $conn->prepare($restore_query);
            $restore_stmt->bind_param("si", $order_id, $farmer_id);
            
            if (!$restore_stmt->execute()) {
                error_log("Failed to restore crop quantity: " . $conn->error);
                throw new Exception("Failed to restore crop quantity");
            }
        }
        
        // Commit transaction
        $conn->commit();
        header("Location: verify_orders.php?success=1");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Error: " . $e->getMessage());
        header("Location: verify_orders.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include ('fheader.php'); ?>
    <title>Verify Orders - Agriculture Portal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #2E7D32;
            --secondary-color: #4CAF50;
            --accent-color: #81C784;
            --background-color: #F8F9FA;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            --transition-speed: 0.3s;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Poppins', sans-serif;
        }

        .card {
            border: none;
            border-radius: 15px;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
            background: white;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            padding: 1.5rem;
        }

        .card-title {
            color: #2C3E50;
            font-weight: 600;
            margin: 0;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            border-bottom: 2px solid #E0E0E0;
            font-weight: 600;
            color: #2C3E50;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: background-color var(--transition-speed);
        }

        .table tbody tr:hover {
            background-color: #F5F6F7;
        }

        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-pending { 
            background-color: #FFF3E0; 
            color: #E65100; 
        }
        .status-processing { 
            background-color: #E3F2FD; 
            color: #1565C0; 
        }
        .status-completed { 
            background-color: #E8F5E9; 
            color: #2E7D32; 
        }
        .status-cancelled { 
            background-color: #FFEBEE; 
            color: #C62828; 
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all var(--transition-speed);
        }

        .btn-process {
            background-color: #2196F3;
            border-color: #2196F3;
            color: white;
        }

        .btn-process:hover {
            background-color: #1976D2;
            border-color: #1976D2;
            transform: translateY(-2px);
        }

        .btn-complete {
            background-color: #4CAF50;
            border-color: #4CAF50;
            color: white;
        }

        .btn-complete:hover {
            background-color: #388E3C;
            border-color: #388E3C;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background-color: #F44336;
            border-color: #F44336;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #D32F2F;
            border-color: #D32F2F;
            transform: translateY(-2px);
        }

        .customer-details {
            background-color: #F8F9FA;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .search-box {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 1px solid #E0E0E0;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all var(--transition-speed);
        }

        .search-box input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
            outline: none;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #95A5A6;
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
        }

        .empty-state i {
            font-size: 3rem;
            color: #95A5A6;
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            color: #2C3E50;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #95A5A6;
            margin: 0;
        }
    </style>
</head>
<body class="bg-light">
    <?php include ('fnav.php'); ?>
    
    <div class="container mt-5 pt-4">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Order processed successfully!
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Manage Orders</h4>
                <div class="text-muted">
                    Farmer ID: <?php echo htmlspecialchars($farmer_id); ?> | 
                    Name: <?php echo htmlspecialchars($farmer_name); ?>
                </div>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="orderSearch" class="form-control" placeholder="Search orders...">
                </div>

                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h5>No Orders Found</h5>
                        <p>You don't have any orders to manage at the moment.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Crop</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($order['cust_name']); ?>
                                        <div class="small text-muted">
                                            Phone: <?php echo htmlspecialchars($order['customer_phone']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['crop_name']); ?></td>
                                    <td><?php echo number_format($order['quantity'], 2) . ' kg'; ?></td>
                                    <td>₹<?php echo number_format($order['price'], 2); ?></td>
                                    <td>₹<?php echo number_format($order['total_price'], 2); ?></td>
                                    <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Start processing this order?');">
                                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                                                <input type="hidden" name="action" value="process">
                                                <button type="submit" class="btn btn-sm btn-process">
                                                    <i class="fas fa-cog"></i> Process
                                                </button>
                                            </form>
                                        <?php elseif ($order['status'] === 'processing'): ?>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Mark this order as completed?');">
                                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                                                <input type="hidden" name="action" value="complete">
                                                <button type="submit" class="btn btn-sm btn-complete">
                                                    <i class="fas fa-check"></i> Complete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($order['status'] !== 'completed' && $order['status'] !== 'cancelled'): ?>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                                                <input type="hidden" name="action" value="cancel">
                                                <button type="submit" class="btn btn-sm btn-cancel">
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php require("../modern-footer.php"); ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

    <script>
    document.getElementById('orderSearch').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let tableRows = document.querySelectorAll('tbody tr');
        
        tableRows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });
    </script>
</body>
</html>
