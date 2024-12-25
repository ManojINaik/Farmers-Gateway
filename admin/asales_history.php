<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("location: ../login.php");
    exit;
}
include("../Includes/db.php");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales History</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.bootstrap4.min.css">
    
    <style>
        body {
            background: #f4f6f9;
        }
        .content-wrapper {
            margin-top: 60px;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .card-header {
            background: #2ecc71;
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .table thead th {
            border-bottom: 2px solid #2ecc71;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .stats-card i {
            font-size: 2rem;
            color: #2ecc71;
        }
        .stats-card .number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
        }
        .stats-card .label {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <?php include("anav.php"); ?>

    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="number">
                                    <?php
                                    $query = "SELECT COUNT(*) as total FROM crop_trades WHERE status = 'Completed'";
                                    $result = mysqli_query($connection, $query);
                                    if ($result && $row = mysqli_fetch_assoc($result)) {
                                        echo number_format($row['total']);
                                    } else {
                                        echo '0';
                                    }
                                    ?>
                                </div>
                                <div class="label">Total Trades</div>
                            </div>
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="number">
                                    <?php
                                    $query = "SELECT COALESCE(SUM(total_amount), 0) as revenue FROM crop_trades WHERE status = 'Completed'";
                                    $result = mysqli_query($connection, $query);
                                    if ($result && $row = mysqli_fetch_assoc($result)) {
                                        echo '₹' . number_format($row['revenue'], 2);
                                    } else {
                                        echo '₹0.00';
                                    }
                                    ?>
                                </div>
                                <div class="label">Total Revenue</div>
                            </div>
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="number">
                                    <?php
                                    $query = "SELECT COUNT(DISTINCT farmer_id) as farmers FROM crop_trades WHERE status = 'Completed'";
                                    $result = mysqli_query($connection, $query);
                                    if ($result && $row = mysqli_fetch_assoc($result)) {
                                        echo number_format($row['farmers']);
                                    } else {
                                        echo '0';
                                    }
                                    ?>
                                </div>
                                <div class="label">Active Farmers</div>
                            </div>
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="number">
                                    <?php
                                    $query = "SELECT COUNT(DISTINCT customer_id) as customers FROM crop_trades WHERE status = 'Completed'";
                                    $result = mysqli_query($connection, $query);
                                    if ($result && $row = mysqli_fetch_assoc($result)) {
                                        echo number_format($row['customers']);
                                    } else {
                                        echo '0';
                                    }
                                    ?>
                                </div>
                                <div class="label">Active Customers</div>
                            </div>
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales History Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-2"></i>
                                Sales History
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="salesTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Transaction ID</th>
                                            <th>Farmer</th>
                                            <th>Customer</th>
                                            <th>Crop</th>
                                            <th>Quantity (kg)</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT ct.*, f.farmer_name, c.customer_name 
                                                FROM crop_trades ct
                                                LEFT JOIN farmerlogin f ON ct.farmer_id = f.farmer_id
                                                LEFT JOIN customerlogin c ON ct.customer_id = c.customer_id
                                                WHERE ct.status = 'Completed'
                                                ORDER BY ct.trade_date DESC";
                                        $result = mysqli_query($connection, $query);

                                        if ($result && mysqli_num_rows($result) > 0) {
                                            while($row = mysqli_fetch_assoc($result)) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $row['trade_id']; ?></td>
                                                    <td><?php echo htmlspecialchars($row['farmer_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['crop_name']); ?></td>
                                                    <td><?php echo number_format($row['quantity']); ?></td>
                                                    <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
                                                    <td><?php echo date('d M Y', strtotime($row['trade_date'])); ?></td>
                                                    <td><span class="text-success">Completed</span></td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="8" class="text-center">No completed trades found.</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

    <script>
    $(document).ready(function() {
        // Initialize DataTable with export buttons
        $('#salesTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn btn-info btn-sm',
                    exportOptions: {
                        columns: [0,1,2,3,4,5,6]
                    }
                }
            ],
            "order": [[6, "desc"]]
        });
    });
    </script>
</body>
</html>
