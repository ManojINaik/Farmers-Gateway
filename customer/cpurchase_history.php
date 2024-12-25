<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('../sql.php');
require_once('csession.php');

// Check if user is logged in
if (!isset($_SESSION['customer_login_user'])) {
    header("Location: ../index.php");
    exit();
}

$customer_id = $_SESSION['cust_id'];

// Get purchase history with farmer details
$query = "SELECT o.*, oh.*, f.farmer_name, f.phone_no as farmer_contact 
          FROM orders o 
          INNER JOIN order_history oh ON o.order_id = oh.order_id
          LEFT JOIN farmerlogin f ON oh.farmer_id = f.farmer_id
          WHERE o.customer_id = ?
          ORDER BY o.order_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = array();
while($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History - FarmersGateway</title>
    
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include FontAwesome -->
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

        .farmer-info {
            display: flex;
            align-items: center;
        }

        .farmer-info i {
            margin-right: 0.5rem;
            color: #95A5A6;
        }

        .price-col {
            font-weight: 500;
            color: #2C3E50;
        }

        .total-col {
            font-weight: 600;
            color: var(--primary-color);
        }
    </style>
</head>
<body class="bg-light">
    <?php include('cnav.php'); ?>
    
    <div class="container mt-5 pt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Purchase History</h4>
                <div class="text-muted">
                    Customer ID: <?php echo htmlspecialchars($customer_id); ?>
                </div>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="orderSearch" class="form-control" placeholder="Search purchases...">
                </div>

                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag"></i>
                        <h5>No Purchase History</h5>
                        <p>You haven't made any purchases yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Crop</th>
                                    <th>Farmer</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                        <td><?php echo htmlspecialchars($order['crop_name']); ?></td>
                                        <td class="farmer-info">
                                            <div>
                                                <div><?php echo htmlspecialchars($order['farmer_name']); ?></div>
                                                <div class="small text-muted">
                                                    <i class="fas fa-phone-alt"></i> <?php echo htmlspecialchars($order['farmer_contact']); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo number_format($order['quantity'], 2) . ' kg'; ?></td>
                                        <td class="price-col">₹<?php echo number_format($order['price'], 2); ?></td>
                                        <td class="total-col">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                        <td>
                                            <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                                            </span>
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
