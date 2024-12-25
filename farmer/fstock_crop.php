<?php
include ('fsession.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['farmer_login_user'])){
header("location: ../index.php");} 
$query4 = "SELECT * from farmerlogin where email='$user_check'";
              $ses_sq4 = mysqli_query($conn, $query4);
              $row4 = mysqli_fetch_assoc($ses_sq4);
              $para1 = $row4['farmer_id'];
              $para2 = $row4['farmer_name'];
?>
<!DOCTYPE html>
<html>
<?php include ('fheader.php'); ?>
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .stock-card {
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .stock-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .btn-floating {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(45deg, #43a047, #1b5e20);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 999;
        }
        
        .btn-floating:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(45deg, #43a047, #1b5e20);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #43a047;
            box-shadow: 0 0 0 0.2rem rgba(67, 160, 71, 0.25);
        }
        
        .btn-success {
            background: linear-gradient(45deg, #43a047, #1b5e20);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(67, 160, 71, 0.3);
        }
        
        .stock-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-high {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-medium {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-low {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 3px;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        
        .edit-btn {
            background-color: #ffc107;
            color: white;
        }
    </style>
</head>

<body class="bg-white" id="top">
    <?php include ('fnav.php'); ?>
    
    <section class="section section-shaped section-lg">
        <div class="shape shape-style-1 bg-gradient-success">
            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
        </div>
        
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="text-center mb-5 fade-in">
                        <h2 class="text-white display-4">Crop Inventory</h2>
                        <p class="text-white-50">Monitor and manage your available crop stock</p>
                    </div>
                    
                    <div class="stock-card bg-white fade-in">
                        <div class="card-header bg-white py-4">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">
                                        <i class="fas fa-warehouse mr-2 text-success"></i>Available Crops
                                    </h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-items-center table-hover" id="cropTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><i class="fas fa-seedling mr-2"></i>Crop Name</th>
                                            <th><i class="fas fa-weight mr-2"></i>Quantity (kg)</th>
                                            <th><i class="fas fa-chart-bar mr-2"></i>Stock Status</th>
                                            <th><i class="fas fa-tools mr-2"></i>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT crop, quantity FROM production_approx";
                                        $query = mysqli_query($conn, $sql);
                                        
                                        while($res = mysqli_fetch_array($query)) {
                                            $status_class = '';
                                            $status_text = '';
                                            
                                            if($res['quantity'] > 1000) {
                                                $status_class = 'status-high';
                                                $status_text = 'High Stock';
                                            } elseif($res['quantity'] > 500) {
                                                $status_class = 'status-medium';
                                                $status_text = 'Medium Stock';
                                            } else {
                                                $status_class = 'status-low';
                                                $status_text = 'Low Stock';
                                            }
                                        ?>
                                            <tr class="fade-in">
                                                <td class="font-weight-bold"><?php echo $res['crop']; ?></td>
                                                <td><?php echo number_format($res['quantity']); ?> kg</td>
                                                <td><span class="stock-status <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                                <td>
                                                    <button class="btn action-btn edit-btn" onclick="editCrop('<?php echo $res['crop']; ?>', <?php echo $res['quantity']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn action-btn delete-btn" onclick="deleteCrop('<?php echo $res['crop']; ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
        
        <!-- Floating Add Button -->
        <button class="btn-floating" data-toggle="modal" data-target="#addCropModal">
            <i class="fas fa-plus"></i>
        </button>
    </section>
    
    <!-- Add Crop Modal -->
    <div class="modal fade" id="addCropModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Crop</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addCropForm">
                        <div class="form-group">
                            <label>Crop Name</label>
                            <input type="text" class="form-control" name="crop_name" required>
                        </div>
                        <div class="form-group">
                            <label>Quantity (kg)</label>
                            <input type="number" class="form-control" name="quantity" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Add Crop</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Crop Modal -->
    <div class="modal fade" id="editCropModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Crop</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editCropForm">
                        <input type="hidden" name="original_crop_name">
                        <div class="form-group">
                            <label>Crop Name</label>
                            <input type="text" class="form-control" name="crop_name" required>
                        </div>
                        <div class="form-group">
                            <label>Quantity (kg)</label>
                            <input type="number" class="form-control" name="quantity" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Update Crop</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php require("../modern-footer.php"); ?>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#cropTable').DataTable({
                "order": [[0, "asc"]],
                "pageLength": 10,
                "language": {
                    "search": "<i class='fas fa-search'></i> Search:",
                    "paginate": {
                        "next": '<i class="fas fa-angle-right"></i>',
                        "previous": '<i class="fas fa-angle-left"></i>'
                    }
                }
            });
            
            // Add Crop Form Submit
            $('#addCropForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'crop_actions.php',
                    type: 'POST',
                    data: {
                        action: 'add',
                        crop_name: $('input[name="crop_name"]').val(),
                        quantity: $('input[name="quantity"]').val()
                    },
                    success: function(response) {
                        if(response.success) {
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    }
                });
            });
            
            // Edit Crop Form Submit
            $('#editCropForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'crop_actions.php',
                    type: 'POST',
                    data: {
                        action: 'edit',
                        original_crop_name: $('input[name="original_crop_name"]').val(),
                        crop_name: $('#editCropForm input[name="crop_name"]').val(),
                        quantity: $('#editCropForm input[name="quantity"]').val()
                    },
                    success: function(response) {
                        if(response.success) {
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    }
                });
            });
        });
        
        function editCrop(cropName, quantity) {
            $('#editCropForm input[name="original_crop_name"]').val(cropName);
            $('#editCropForm input[name="crop_name"]').val(cropName);
            $('#editCropForm input[name="quantity"]').val(quantity);
            $('#editCropModal').modal('show');
        }
        
        function deleteCrop(cropName) {
            if(confirm('Are you sure you want to delete this crop?')) {
                $.ajax({
                    url: 'crop_actions.php',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        crop_name: cropName
                    },
                    success: function(response) {
                        if(response.success) {
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>
