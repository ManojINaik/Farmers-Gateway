<?php
session_start();// Starting Session
require('../sql.php'); // Includes Login Script

// Storing Session
$user = $_SESSION['admin_login_user'];

if(!isset($_SESSION['admin_login_user'])){
    header("location: ../index.php");
}

$query4 = "SELECT * from admin where admin_name ='$user'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['admin_id'];
$para2 = $row4['admin_name'];
$para3 = $row4['admin_password'];
?>

<!DOCTYPE html>
<html lang="en">
<?php require ('aheader.php'); ?>

<head>
    <link href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css" rel="stylesheet">
    <style>
        .crop-stats {
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #4CAF50;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #636e72;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .crop-header {
            margin-bottom: 2rem;
            text-align: center;
            padding: 0 1rem;
        }
        
        .crop-header h2 {
            color: #2d3436;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .crop-header p {
            color: #636e72;
            font-size: 1.1rem;
        }
        
        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            padding: 1.5rem;
            margin-bottom: 2rem;
            width: 100%;
        }
        
        .chart-container {
            position: relative;
            width: 100%;
            min-height: 300px;
            height: 50vh;
            max-height: 500px;
            margin-bottom: 2rem;
        }
        
        canvas#cropChart {
            width: 100% !important;
            height: 100% !important;
        }
        
        @media (max-width: 991.98px) {
            .chart-container {
                height: 40vh;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
            
            .stat-value {
                font-size: 1.75rem;
            }
            
            .stat-icon {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 767.98px) {
            .chart-container {
                height: 35vh;
            }
            
            .crop-stats {
                padding: 1rem 0.5rem;
            }
            
            .stat-value {
                font-size: 1.5rem;
            }
            
            .crop-header h2 {
                font-size: 1.75rem;
            }
            
            .crop-header p {
                font-size: 1rem;
            }
            
            .table-card {
                padding: 1rem;
                margin: 0.5rem;
                width: calc(100% - 1rem);
            }
        }
        
        @media (max-width: 575.98px) {
            .chart-container {
                height: 30vh;
                min-height: 250px;
            }
            
            .stat-card {
                padding: 1rem;
            }
            
            .stat-value {
                font-size: 1.25rem;
            }
            
            .stat-label {
                font-size: 0.85rem;
            }
        }
        
        /* Table styles */
        .table-responsive {
            margin: 0;
            padding: 0;
            width: 100%;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        @media (max-width: 767.98px) {
            .table th, 
            .table td {
                font-size: 0.9rem;
                padding: 0.5rem;
            }
        }
    </style>
</head>

<body class="bg-light">
    <?php require ('anav.php'); ?>

    <div class="container py-5">
        <div class="crop-header">
            <h2>Crop Production Overview</h2>
            <p>Monitor and analyze current crop production levels</p>
        </div>

        <!-- Statistics Row -->
        <div class="row crop-stats">
            <?php
            $total_crops = mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT crop FROM production_approx WHERE quantity > 0"));
            $total_quantity = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(quantity) as total FROM production_approx WHERE quantity > 0"))['total'];
            $avg_quantity = mysqli_fetch_array(mysqli_query($conn, "SELECT AVG(quantity) as avg FROM production_approx WHERE quantity > 0"))['avg'];
            ?>
            <div class="col-md-4 mb-4">
                <div class="stat-card text-center">
                    <i class="fas fa-seedling stat-icon"></i>
                    <div class="stat-value"><?php echo $total_crops; ?></div>
                    <div class="stat-label">Active Crops</div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="stat-card text-center">
                    <i class="fas fa-weight-hanging stat-icon"></i>
                    <div class="stat-value"><?php echo number_format($total_quantity); ?></div>
                    <div class="stat-label">Total Quantity (KG)</div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="stat-card text-center">
                    <i class="fas fa-chart-line stat-icon"></i>
                    <div class="stat-value"><?php echo number_format($avg_quantity, 1); ?></div>
                    <div class="stat-label">Average Quantity (KG)</div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="row">
            <div class="col-12">
                <div class="table-card">
                    <div class="chart-container">
                        <canvas id="cropChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Crops Table -->
        <div class="table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Production Details</h4>
                <button class="btn btn-success" onclick="exportTableToExcel()">
                    <i class="fas fa-file-excel mr-2"></i>Export to Excel
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>Crop Name</th>
                            <th>Quantity (KG)</th>
                            <th>Production Share</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT crop, quantity, 
                               (quantity / (SELECT SUM(quantity) FROM production_approx WHERE quantity > 0) * 100) as percentage 
                               FROM production_approx WHERE quantity > 0 ORDER BY quantity DESC";
                        $query = mysqli_query($conn, $sql);
                        while($res = mysqli_fetch_array($query)) {
                            $percentage = round($res['percentage'], 1);
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($res['crop']); ?>&background=random" 
                                         alt="<?php echo $res['crop']; ?>" 
                                         class="rounded-circle mr-2" 
                                         style="width: 30px; height: 30px;">
                                    <span class="crop-badge">
                                        <?php echo $res['crop']; ?>
                                    </span>
                                </div>
                            </td>
                            <td class="quantity-cell">
                                <?php echo number_format($res['quantity']); ?> KG
                            </td>
                            <td style="width: 30%;">
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 mr-2">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?php echo $percentage; ?>%" 
                                             aria-valuenow="<?php echo $percentage; ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="text-muted"><?php echo $percentage; ?>%</span>
                                </div>
                            </td>
                            <td>
                                <?php 
                                if($res['quantity'] > $avg_quantity) {
                                    echo '<span class="badge badge-success">High Production</span>';
                                } else {
                                    echo '<span class="badge badge-warning">Normal Production</span>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php require("../modern-footer.php"); ?>

    <!-- Additional Scripts -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#myTable').DataTable({
                "pageLength": 10,
                "order": [[1, "desc"]],
                "language": {
                    "search": "Search crops:",
                    "lengthMenu": "Show _MENU_ crops per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ crops"
                }
            });

            // Initialize Chart
            <?php
            $chart_query = mysqli_query($conn, "SELECT crop, quantity FROM production_approx WHERE quantity > 0 ORDER BY quantity DESC LIMIT 5");
            $crops = [];
            $quantities = [];
            while($row = mysqli_fetch_array($chart_query)) {
                $crops[] = $row['crop'];
                $quantities[] = $row['quantity'];
            }
            ?>

            const ctx = document.getElementById('cropChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($crops); ?>,
                    datasets: [{
                        label: 'Production Quantity (KG)',
                        data: <?php echo json_encode($quantities); ?>,
                        backgroundColor: '#4CAF50',
                        borderColor: '#2E7D32',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Top 5 Crops by Production',
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Quantity (KG)'
                            }
                        }
                    }
                }
            });
        });

        function exportTableToExcel() {
            let table = document.getElementById("myTable");
            let html = table.outerHTML;
            let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
            let downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);
            downloadLink.href = url;
            downloadLink.download = 'crop_production.xls';
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('cropChart').getContext('2d');
            
            // Make sure Chart.js respects container size
            ctx.canvas.style.width = '100%';
            ctx.canvas.style.height = '100%';
            
            <?php
            $chart_query = "SELECT crop, quantity FROM production_approx WHERE quantity > 0 ORDER BY quantity DESC LIMIT 10";
            $chart_result = mysqli_query($conn, $chart_query);
            
            $labels = [];
            $data = [];
            
            while($row = mysqli_fetch_assoc($chart_result)) {
                $labels[] = $row['crop'];
                $data[] = $row['quantity'];
            }
            ?>
            
            var cropChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Crop Production (KG)',
                        data: <?php echo json_encode($data); ?>,
                        backgroundColor: 'rgba(76, 175, 80, 0.6)',
                        borderColor: 'rgba(76, 175, 80, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: function() {
                                        return window.innerWidth < 768 ? 10 : 12;
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: function() {
                                        return window.innerWidth < 768 ? 10 : 12;
                                    }
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: function() {
                                        return window.innerWidth < 768 ? 10 : 12;
                                    }
                                }
                            }
                        }
                    }
                }
            });
            
            // Handle resize
            window.addEventListener('resize', function() {
                cropChart.resize();
            });
        });
    </script>
</body>
</html>
