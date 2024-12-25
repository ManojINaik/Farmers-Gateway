<?php
include ('fsession.php');
require_once("../sql.php");

if(!isset($_SESSION['farmer_login_user'])){
    header("location: ../index.php");
    exit();
} 

// Get farmer ID from session
$user_check = $_SESSION['farmer_login_user'];
$query = "SELECT farmer_id FROM farmerlogin WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_check);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$farmer_id = $row['farmer_id'];

// Get order counts by status
$status_counts_query = "SELECT status, COUNT(*) as count 
                       FROM orders o 
                       JOIN order_history oh ON o.order_id = oh.order_id 
                       WHERE oh.farmer_id = ? 
                       GROUP BY status";
$status_stmt = $conn->prepare($status_counts_query);
$status_stmt->bind_param("i", $farmer_id);
$status_stmt->execute();
$status_result = $status_stmt->get_result();

$status_counts = array(
    'pending' => 0,
    'processing' => 0,
    'completed' => 0,
    'cancelled' => 0
);

while ($row = $status_result->fetch_assoc()) {
    $status_counts[strtolower($row['status'])] = $row['count'];
}

// Get total earnings (only from completed orders)
$earnings_query = "SELECT COALESCE(SUM(oh.total_price), 0) as total_earnings 
                  FROM order_history oh 
                  JOIN orders o ON o.order_id = oh.order_id 
                  WHERE oh.farmer_id = ? AND o.status = 'completed'";
$earnings_stmt = $conn->prepare($earnings_query);
$earnings_stmt->bind_param("i", $farmer_id);
$earnings_stmt->execute();
$earnings_result = $earnings_stmt->get_result();
$total_earnings = $earnings_result->fetch_assoc()['total_earnings'];

// Get unique crops count
$crops_query = "SELECT COUNT(DISTINCT crop_name) as crop_count 
                FROM order_history 
                WHERE farmer_id = ?";
$crops_stmt = $conn->prepare($crops_query);
$crops_stmt->bind_param("i", $farmer_id);
$crops_stmt->execute();
$crops_result = $crops_stmt->get_result();
$unique_crops = $crops_result->fetch_assoc()['crop_count'];

// Get orders with full details
$query = "SELECT oh.*, o.status, o.order_date, cl.cust_name
          FROM order_history oh
          INNER JOIN orders o ON o.order_id = oh.order_id
          LEFT JOIN custlogin cl ON cl.cust_id = o.customer_id
          WHERE oh.farmer_id = ?
          ORDER BY o.order_date DESC";

error_log("Executing query: " . str_replace('?', $farmer_id, $query));
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$orders_result = $stmt->get_result();

// Store orders in array for table display
$orders = array();
while ($row = $orders_result->fetch_assoc()) {
    // Debug raw data
    error_log("Raw order data: " . print_r($row, true));
    
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include ('fheader.php'); ?>
    <title>Selling History - Agriculture Portal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
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

        .stats-card {
            border: none;
            border-radius: 15px;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
            background: white;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .card-body {
            padding: 1.5rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .stat-icon.earnings {
            background-color: rgba(46, 125, 50, 0.1);
            color: var(--primary-color);
        }

        .stat-icon.orders {
            background-color: rgba(33, 150, 243, 0.1);
            color: #2196F3;
        }

        .stat-icon.crops {
            background-color: rgba(156, 39, 176, 0.1);
            color: #9C27B0;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0.5rem 0;
            color: #2C3E50;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #95A5A6;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-count {
            display: flex;
            justify-content: space-between;
            margin: 0.25rem 0;
            font-size: 0.9rem;
        }

        .status-count .count {
            font-weight: 500;
            color: #2C3E50;
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

        .table {
            background: white;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            margin-top: 2rem;
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
    </style>
</head>
<body class="bg-light">
    <?php include ('fnav.php'); ?>
    
    <div class="container mt-5 pt-4">
        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Earnings Card -->
            <div class="col-md-4">
                <div class="stats-card h-100">
                    <div class="card-body">
                        <div class="stat-icon earnings">
                            <i class="fas fa-rupee-sign fa-lg"></i>
                        </div>
                        <div class="stat-label">Total Earnings</div>
                        <div class="stat-value">₹<?php echo number_format($total_earnings, 2); ?></div>
                        <div class="stat-description text-muted">From completed orders</div>
                    </div>
                </div>
            </div>

            <!-- Orders Overview Card -->
            <div class="col-md-4">
                <div class="stats-card h-100">
                    <div class="card-body">
                        <div class="stat-icon orders">
                            <i class="fas fa-clipboard-list fa-lg"></i>
                        </div>
                        <div class="stat-label">Orders Overview</div>
                        <div class="status-count">
                            <span>Pending</span>
                            <span class="count"><?php echo $status_counts['pending']; ?></span>
                        </div>
                        <div class="status-count">
                            <span>Processing</span>
                            <span class="count"><?php echo $status_counts['processing']; ?></span>
                        </div>
                        <div class="status-count">
                            <span>Completed</span>
                            <span class="count"><?php echo $status_counts['completed']; ?></span>
                        </div>
                        <div class="status-count">
                            <span>Cancelled</span>
                            <span class="count"><?php echo $status_counts['cancelled']; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unique Crops Card -->
            <div class="col-md-4">
                <div class="stats-card h-100">
                    <div class="card-body">
                        <div class="stat-icon crops">
                            <i class="fas fa-seedling fa-lg"></i>
                        </div>
                        <div class="stat-label">Unique Crops</div>
                        <div class="stat-value"><?php echo $unique_crops; ?></div>
                        <div class="stat-description text-muted">Different crops sold</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table Section -->
        <div class="card stats-card">
            <div class="card-body">
                <h5 class="card-title mb-4">Orders History</h5>
                
                <!-- Search Box -->
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="orderSearch" class="form-control" placeholder="Search orders...">
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Crop Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($orders as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['crop_name']); ?></td>
                                <td><?php echo number_format($row['quantity'], 2) . ' kg'; ?></td>
                                <td>₹<?php echo number_format($row['price'], 2); ?></td>
                                <td>₹<?php echo number_format($row['total_price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['cust_name']); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['order_date'])); ?></td>
                                <td>
                                    <span class="order-status status-<?php echo strtolower($row['status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            order: [[6, 'desc']], // Sort by date column by default
            language: {
                search: "Search orders:"
            }
        });
    });
    </script>

    <script>
    document.getElementById('orderSearch').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let tableRows = document.querySelectorAll('tbody tr');
        
        tableRows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    </script>
</body>
</html>
