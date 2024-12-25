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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .customer-stats {
            padding: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #3498db;
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #636e72;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card-header {
            background: transparent;
            border-bottom: none;
            padding: 1rem 1.5rem;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            border-top: none;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: #2d3436;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }
        
        .table td {
            vertical-align: middle;
            color: #636e72;
            font-size: 0.9rem;
        }
        
        .btn-delete {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        
        .btn-delete:hover {
            background-color: #e74c3c;
            transform: translateY(-2px);
        }
        
        .customer-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .customer-header {
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .customer-header h2 {
            color: #2d3436;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .customer-header p {
            color: #636e72;
            font-size: 1.1rem;
        }
        
        .address-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .address-cell:hover {
            white-space: normal;
            overflow: visible;
        }
        
        @media (max-width: 768px) {
            .stat-card {
                margin-bottom: 1rem;
            }
            
            .table-responsive {
                border-radius: 15px;
            }
        }
    </style>
</head>

<body class="bg-light">
    <?php require ('anav.php'); ?>

    <div class="container-fluid py-5">
        <div class="customer-header">
            <h2>Customer Management</h2>
            <p>View and manage all registered customers in the system</p>
        </div>

        <!-- Statistics Row -->
        <div class="row customer-stats">
            <?php
            $total_customers = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM custlogin"));
            $total_states = mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT state FROM custlogin"));
            $total_cities = mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT city FROM custlogin"));
            ?>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fas fa-users stat-icon"></i>
                    <div class="stat-value"><?php echo $total_customers; ?></div>
                    <div class="stat-label">Total Customers</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fas fa-map-marker-alt stat-icon"></i>
                    <div class="stat-value"><?php echo $total_states; ?></div>
                    <div class="stat-label">States Covered</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card text-center">
                    <i class="fas fa-city stat-icon"></i>
                    <div class="stat-value"><?php echo $total_cities; ?></div>
                    <div class="stat-label">Cities Reached</div>
                </div>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Customers List</h4>
                <button class="btn btn-primary" onclick="exportTableToExcel()">
                    <i class="fas fa-file-excel mr-2"></i>Export to Excel
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact Info</th>
                            <th>Location</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT c.cust_name, c.cust_id, c.email, c.phone_no, s.StateName, c.city, c.address, c.pincode 
                                FROM custlogin c 
                                LEFT JOIN state s ON c.state = s.StCode 
                                ORDER BY c.cust_id DESC";
                        $query = mysqli_query($conn, $sql);
                        while($res = mysqli_fetch_array($query)) {
                        ?>
                        <tr>
                            <td><?php echo $res['cust_id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($res['cust_name']); ?>&background=random" 
                                         alt="<?php echo $res['cust_name']; ?>" 
                                         class="rounded-circle mr-2" 
                                         style="width: 30px; height: 30px;">
                                    <?php echo $res['cust_name']; ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <i class="fas fa-envelope mr-1 text-muted"></i> <?php echo $res['email']; ?>
                                </div>
                                <div>
                                    <i class="fas fa-phone mr-1 text-muted"></i> <?php echo $res['phone_no']; ?>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="customer-badge bg-light">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        <?php echo $res['city']; ?>, <?php echo $res['StateName']; ?>
                                    </span>
                                </div>
                                <div class="text-muted mt-1">
                                    <small>PIN: <?php echo $res['pincode']; ?></small>
                                </div>
                            </td>
                            <td>
                                <div class="address-cell" title="<?php echo $res['address']; ?>">
                                    <?php echo $res['address']; ?>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-danger btn-delete" 
                                        onclick="confirmDelete(<?php echo $res['cust_id']; ?>)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                "pageLength": 10,
                "order": [[0, "desc"]],
                "language": {
                    "search": "Search customers:",
                    "lengthMenu": "Show _MENU_ customers per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ customers"
                }
            });
        });

        function confirmDelete(custId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to delete this customer?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Use jQuery AJAX for better error handling
                    $.ajax({
                        url: 'acdelete.php',
                        type: 'GET',
                        data: { id: custId },
                        success: function(response) {
                            // Parse the response and show appropriate message
                            if (response.includes('Customer Deleted Successfully')) {
                                Swal.fire(
                                    'Deleted!',
                                    'Customer has been deleted.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Unable to delete customer.',
                                    'error'
                                );
                            }
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'Something went wrong.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function exportTableToExcel() {
            let table = document.getElementById("myTable");
            let html = table.outerHTML;
            let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
            let downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);
            downloadLink.href = url;
            downloadLink.download = 'customers_list.xls';
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
</body>
</html>