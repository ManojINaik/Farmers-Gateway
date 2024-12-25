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
    <title>Crop Requests</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    
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
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 0 0.2rem;
        }
    </style>
</head>

<body>
    <?php include("anav.php"); ?>

    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clipboard-list mr-2"></i>
                                Crop Requests Management
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="requestsTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Request ID</th>
                                            <th>Farmer Name</th>
                                            <th>Crop Name</th>
                                            <th>Quantity (kg)</th>
                                            <th>Price per kg</th>
                                            <th>Request Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT cr.*, f.farmer_name 
                                                FROM crop_requests cr
                                                JOIN farmerlogin f ON cr.farmer_id = f.farmer_id
                                                ORDER BY cr.request_date DESC";
                                        $result = mysqli_query($connection, $query);

                                        if ($result && mysqli_num_rows($result) > 0) {
                                            while($row = mysqli_fetch_assoc($result)) {
                                                $status_class = '';
                                                switch($row['status']) {
                                                    case 'Pending':
                                                        $status_class = 'text-warning';
                                                        break;
                                                    case 'Approved':
                                                        $status_class = 'text-success';
                                                        break;
                                                    case 'Rejected':
                                                        $status_class = 'text-danger';
                                                        break;
                                                }
                                                ?>
                                                <tr>
                                                    <td><?php echo $row['request_id']; ?></td>
                                                    <td><?php echo htmlspecialchars($row['farmer_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['crop_name']); ?></td>
                                                    <td><?php echo $row['quantity']; ?></td>
                                                    <td>₹<?php echo $row['price_per_kg']; ?></td>
                                                    <td><?php echo date('d M Y', strtotime($row['request_date'])); ?></td>
                                                    <td><span class="<?php echo $status_class; ?>"><?php echo $row['status']; ?></span></td>
                                                    <td>
                                                        <?php if($row['status'] === 'Pending'): ?>
                                                        <button class="btn btn-success btn-action approve-btn" data-id="<?php echo $row['request_id']; ?>">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-danger btn-action reject-btn" data-id="<?php echo $row['request_id']; ?>">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="8" class="text-center">No requests found.</td></tr>';
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#requestsTable').DataTable({
            "order": [[5, "desc"]]
        });

        // Handle Approve button
        $('.approve-btn').click(function() {
            const requestId = $(this).data('id');
            Swal.fire({
                title: 'Approve Request?',
                text: "Are you sure you want to approve this crop request?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2ecc71',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateRequestStatus(requestId, 'Approved');
                }
            });
        });

        // Handle Reject button
        $('.reject-btn').click(function() {
            const requestId = $(this).data('id');
            Swal.fire({
                title: 'Reject Request?',
                text: "Are you sure you want to reject this crop request?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Yes, reject it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateRequestStatus(requestId, 'Rejected');
                }
            });
        });

        function updateRequestStatus(requestId, status) {
            $.ajax({
                url: 'update_request_status.php',
                type: 'POST',
                data: {
                    request_id: requestId,
                    status: status
                },
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: `Request has been ${status.toLowerCase()}.`,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Something went wrong.',
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong.',
                        icon: 'error'
                    });
                }
            });
        }
    });
    </script>
</body>
</html>
