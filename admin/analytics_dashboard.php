<?php
session_start();
if (!isset($_SESSION['admin_login_user'])) {
    header("Location: ../index.php");
    exit();
}
require('../includes/db.php');

function executeQuery($connection, $query) {
    $result = mysqli_query($connection, $query);
    if (!$result) {
        error_log("Query failed: " . mysqli_error($connection));
        return false;
    }
    return $result;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #2ecc71;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }

        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .analytics-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .analytics-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .stat-card {
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
            z-index: 1;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }

        .stat-card p {
            margin: 0;
            opacity: 0.9;
            font-size: 1.1rem;
            position: relative;
            z-index: 2;
        }

        .table-responsive {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .table th {
            font-weight: 600;
            color: var(--dark-color);
            border-bottom-width: 2px;
        }

        .table td {
            vertical-align: middle;
        }

        .disease-alert {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .disease-alert:hover {
            transform: translateX(5px);
        }

        .dashboard-header {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .dashboard-header h2 {
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
        }

        .success-rate-indicator {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .chart-container {
            position: relative;
            margin: auto;
            height: 300px;
        }
    </style>
</head>
<body>
    <?php include('anav.php'); ?>

    <div class="container-fluid py-4" style="margin-top: 80px;">
        <div class="dashboard-header">
            <h2><i class="fas fa-chart-line me-2"></i>Analytics Dashboard</h2>
        </div>
        
        <!-- Summary Statistics -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(45deg, #2ecc71, #27ae60);">
                    <?php
                    $result = executeQuery($connection, "SELECT COUNT(*) as count FROM farmerlogin");
                    $row = $result ? mysqli_fetch_assoc($result) : ['count' => 0];
                    ?>
                    <h3><?php echo number_format($row['count']); ?></h3>
                    <p><i class="fas fa-users me-2"></i>Total Farmers</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(45deg, #3498db, #2980b9);">
                    <?php
                    $result = executeQuery($connection, "SELECT COUNT(*) as count FROM customerlogin");
                    $row = $result ? mysqli_fetch_assoc($result) : ['count' => 0];
                    ?>
                    <h3><?php echo number_format($row['count']); ?></h3>
                    <p><i class="fas fa-shopping-cart me-2"></i>Total Customers</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(45deg, #e74c3c, #c0392b);">
                    <?php
                    $result = executeQuery($connection, "SELECT COUNT(*) as count FROM crop_trade");
                    $row = $result ? mysqli_fetch_assoc($result) : ['count' => 0];
                    ?>
                    <h3><?php echo number_format($row['count']); ?></h3>
                    <p><i class="fas fa-exchange-alt me-2"></i>Total Trades</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(45deg, #9b59b6, #8e44ad);">
                    <?php
                    $result = executeQuery($connection, "SELECT SUM(quantity * price) as total FROM crop_trade WHERE status='completed'");
                    $row = $result ? mysqli_fetch_assoc($result) : ['total' => 0];
                    ?>
                    <h3>₹<?php echo number_format($row['total'] ?? 0); ?></h3>
                    <p><i class="fas fa-rupee-sign me-2"></i>Total Sales Value</p>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Farmer Analytics -->
            <div class="col-md-6">
                <div class="analytics-card">
                    <h4><i class="fas fa-award me-2"></i>Top Performing Farmers</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Farmer Name</th>
                                    <th>Total Sales</th>
                                    <th>Crops Sold</th>
                                    <th>Success Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT 
                                            f.farmer_name,
                                            COUNT(ct.id) as total_trades,
                                            SUM(ct.quantity * ct.price) as total_sales,
                                            COUNT(CASE WHEN ct.status = 'completed' THEN 1 END) as successful_trades
                                        FROM farmerlogin f
                                        LEFT JOIN crop_trade ct ON f.email = ct.farmer_email
                                        GROUP BY f.email
                                        ORDER BY total_sales DESC
                                        LIMIT 5";
                                $result = executeQuery($connection, $query);
                                if ($result) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $success_rate = ($row['total_trades'] > 0) ? 
                                            round(($row['successful_trades'] / $row['total_trades']) * 100) : 0;
                                        
                                        $rate_color = $success_rate >= 80 ? '#2ecc71' : ($success_rate >= 60 ? '#f1c40f' : '#e74c3c');
                                        
                                        echo "<tr>
                                                <td>{$row['farmer_name']}</td>
                                                <td>₹" . number_format($row['total_sales'] ?? 0) . "</td>
                                                <td>{$row['total_trades']}</td>
                                                <td>
                                                    <div class='success-rate-indicator' style='background: {$rate_color}'>
                                                        {$success_rate}%
                                                    </div>
                                                </td>
                                              </tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Plant Disease Detections -->
            <div class="col-md-6">
                <div class="analytics-card">
                    <h4><i class="fas fa-bug me-2"></i>Recent Plant Disease Detections</h4>
                    <div class="disease-alerts">
                        <?php
                        $query = "SELECT 
                                    pd.*, 
                                    f.farmer_name
                                FROM plant_disease_detection pd
                                JOIN farmerlogin f ON pd.farmer_email = f.email
                                ORDER BY pd.detection_date DESC
                                LIMIT 5";
                        $result = executeQuery($connection, $query);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $severity_class = strpos(strtolower($row['disease_name']), 'healthy') !== false ? 
                                    'success' : 'warning';
                                echo "<div class='disease-alert border-{$severity_class}'>
                                        <div class='d-flex justify-content-between align-items-center'>
                                            <strong>{$row['farmer_name']}</strong>
                                            <small class='text-muted'>" . date('d M Y', strtotime($row['detection_date'])) . "</small>
                                        </div>
                                        <div class='mt-2'>
                                            <span class='badge bg-{$severity_class} mb-2'>{$row['disease_name']}</span>
                                            <p class='mb-0'>{$row['description']}</p>
                                        </div>
                                      </div>";
                            }
                        } else {
                            echo "<p class='text-muted'>No recent disease detections</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Crop Analytics -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="analytics-card">
                    <h4><i class="fas fa-chart-bar me-2"></i>Crop Trading Analytics</h4>
                    <div class="chart-container">
                        <canvas id="cropAnalytics"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="analytics-card">
                    <h4><i class="fas fa-history me-2"></i>Recent Transactions</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Farmer</th>
                                    <th>Customer</th>
                                    <th>Crop</th>
                                    <th>Quantity</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT 
                                            ct.*,
                                            f.farmer_name,
                                            c.customer_name,
                                            cr.crop_name
                                        FROM crop_trade ct
                                        JOIN farmerlogin f ON ct.farmer_email = f.email
                                        JOIN customerlogin c ON ct.customer_email = c.email
                                        JOIN crops cr ON ct.crop_id = cr.id
                                        ORDER BY ct.trade_date DESC
                                        LIMIT 10";
                                $result = executeQuery($connection, $query);
                                if ($result) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $status_class = '';
                                        switch ($row['status']) {
                                            case 'completed':
                                                $status_class = 'success';
                                                break;
                                            case 'pending':
                                                $status_class = 'warning';
                                                break;
                                            case 'cancelled':
                                                $status_class = 'danger';
                                                break;
                                        }
                                        echo "<tr>
                                                <td>" . date('d M Y', strtotime($row['trade_date'])) . "</td>
                                                <td>{$row['farmer_name']}</td>
                                                <td>{$row['customer_name']}</td>
                                                <td>{$row['crop_name']}</td>
                                                <td>{$row['quantity']} kg</td>
                                                <td>₹" . number_format($row['quantity'] * $row['price']) . "</td>
                                                <td><span class='badge bg-{$status_class}'>" . ucfirst($row['status']) . "</span></td>
                                              </tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Crop Analytics Chart
        <?php
        $query = "SELECT 
                    cr.crop_name,
                    COUNT(ct.id) as trade_count,
                    SUM(ct.quantity) as total_quantity,
                    SUM(ct.quantity * ct.price) as total_value
                FROM crops cr
                LEFT JOIN crop_trade ct ON cr.id = ct.crop_id
                GROUP BY cr.id
                ORDER BY total_value DESC
                LIMIT 5";
        $result = executeQuery($connection, $query);
        $labels = [];
        $quantities = [];
        $values = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $labels[] = $row['crop_name'];
                $quantities[] = $row['total_quantity'];
                $values[] = $row['total_value'];
            }
        }
        ?>

        const ctx = document.getElementById('cropAnalytics').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Total Quantity (kg)',
                    data: <?php echo json_encode($quantities); ?>,
                    backgroundColor: 'rgba(46, 204, 113, 0.5)',
                    borderColor: 'rgba(46, 204, 113, 1)',
                    borderWidth: 1
                }, {
                    label: 'Total Value (₹)',
                    data: <?php echo json_encode($values); ?>,
                    backgroundColor: 'rgba(52, 152, 219, 0.5)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    </script>
</body>
</html>
