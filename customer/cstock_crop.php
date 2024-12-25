<?php
include ('csession.php');
include ('../sql.php');

ini_set('memory_limit', '-1');

if(!isset($_SESSION['customer_login_user'])){
    header("location: ../index.php");
}

$query4 = "SELECT * from custlogin where email=?";
$stmt = $conn->prepare($query4);
$stmt->bind_param("s", $user_check);
$stmt->execute();
$result = $stmt->get_result();
$row4 = $result->fetch_assoc();
$para1 = $row4['cust_id'];
$para2 = $row4['cust_name'];
?>

<!DOCTYPE html>
<html>
<head>
    <?php include ('cheader.php');  ?>
    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</head>

<body class="bg-white" id="top">
    <?php include ('cnav.php'); ?>
 	
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

        <div class="container py-md">
            <div class="row justify-content-between align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 text-white">Available Crops
                        <span class="text-white">Current Stock</span>
                    </h1>
                    <p class="lead text-white">View our current crop inventory and their available quantities.</p>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-lg-12">
                    <div class="card shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Crop Inventory</h3>
                                </div>
                                <div class="col text-right">
                                    <button id="refreshBtn" class="btn btn-sm btn-success">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-items-center table-flush" id="myTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Crop Name</th>
                                            <th>Available Quantity (KG)</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT crop, quantity FROM production_approx WHERE quantity > 0";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        while($row = $result->fetch_assoc()) {
                                            $stockStatus = '';
                                            $statusClass = '';
                                            
                                            if($row['quantity'] > 100) {
                                                $stockStatus = 'In Stock';
                                                $statusClass = 'success';
                                            } elseif($row['quantity'] > 50) {
                                                $stockStatus = 'Limited Stock';
                                                $statusClass = 'warning';
                                            } else {
                                                $stockStatus = 'Low Stock';
                                                $statusClass = 'danger';
                                            }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <i class="fas fa-seedling text-success mr-3"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0"><?php echo ucfirst($row['crop']); ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold"><?php echo number_format($row['quantity']); ?> KG</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-dot">
                                                    <i class="bg-<?php echo $statusClass; ?>"></i>
                                                    <span class="status"><?php echo $stockStatus; ?></span>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="cbuy_crops.php?crop=<?php echo urlencode($row['crop']); ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-shopping-cart"></i> Buy Now
                                                </a>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include("../modern-footer.php"); ?>
</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
<script>
    $(document).ready(function() {
        // Initialize DataTable with custom options
        const table = $('#myTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[1, 'desc']], // Sort by quantity by default
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            language: {
                search: "<i class='fas fa-search'></i>",
                searchPlaceholder: "Search crops...",
                lengthMenu: "Show _MENU_ crops per page",
                info: "Showing _START_ to _END_ of _TOTAL_ crops",
                infoEmpty: "No crops available",
                infoFiltered: "(filtered from _MAX_ total crops)",
                zeroRecords: "No matching crops found"
            }
        });

        // Refresh button handler
        $('#refreshBtn').click(function() {
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');
            table.ajax.reload(null, false);
            
            setTimeout(() => {
                $(this).html('<i class="fas fa-sync-alt"></i> Refresh');
                Swal.fire({
                    icon: 'success',
                    title: 'Refreshed!',
                    text: 'Crop inventory has been updated.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }, 1000);
        });
    });
</script>

<style>
    .section-shaped {
        position: relative;
        overflow: hidden;
    }
    .bg-gradient-success {
        background: linear-gradient(150deg, #228B22 15%, #32CD32 70%, #90EE90 94%) !important;
    }
    .table td, .table th {
        vertical-align: middle;
        padding: 1rem;
    }
    .badge-dot {
        padding-left: 0;
        padding-right: 0;
        background: transparent;
        font-weight: 400;
        font-size: .875rem;
        text-transform: none;
    }
    .badge-dot i {
        display: inline-block;
        vertical-align: middle;
        width: .375rem;
        height: .375rem;
        border-radius: 50%;
        margin-right: .375rem;
    }
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 20px;
        padding: 5px 15px;
        border: 1px solid #ddd;
        margin-left: 10px;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 20px;
        padding: 5px 30px 5px 15px;
        border: 1px solid #ddd;
    }
    .page-link {
        border-radius: 20px !important;
        margin: 0 3px;
    }
    .btn {
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-1px);
    }
</style>
