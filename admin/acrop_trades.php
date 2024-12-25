<?php
include("asession.php");
include("../Includes/db.php");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Trades</title>
    
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
        .trade-status-active {
            color: #2ecc71;
        }
        .trade-status-completed {
            color: #3498db;
        }
        .trade-status-cancelled {
            color: #e74c3c;
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
                                <i class="fas fa-exchange-alt mr-2"></i>
                                Crop Trades Management
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tradesTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Trade ID</th>
                                            <th>Farmer</th>
                                            <th>Customer</th>
                                            <th>Crop</th>
                                            <th>Quantity (kg)</th>
                                            <th>Total Amount</th>
                                            <th>Trade Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT ct.*, f.farmer_name, c.customer_name 
                                                FROM crop_trades ct
                                                JOIN farmerlogin f ON ct.farmer_id = f.farmer_id
                                                JOIN customerlogin c ON ct.customer_id = c.customer_id
                                                ORDER BY ct.trade_date DESC";
                                        $result = mysqli_query($connection, $query);

                                        while($row = mysqli_fetch_assoc($result)) {
                                            $status_class = '';
                                            switch($row['status']) {
                                                case 'Active':
                                                    $status_class = 'trade-status-active';
                                                    break;
                                                case 'Completed':
                                                    $status_class = 'trade-status-completed';
                                                    break;
                                                case 'Cancelled':
                                                    $status_class = 'trade-status-cancelled';
                                                    break;
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo $row['trade_id']; ?></td>
                                                <td><?php echo $row['farmer_name']; ?></td>
                                                <td><?php echo $row['customer_name']; ?></td>
                                                <td><?php echo $row['crop_name']; ?></td>
                                                <td><?php echo $row['quantity']; ?></td>
                                                <td>₹<?php echo $row['total_amount']; ?></td>
                                                <td><?php echo date('d M Y', strtotime($row['trade_date'])); ?></td>
                                                <td><span class="<?php echo $status_class; ?>"><?php echo $row['status']; ?></span></td>
                                                <td>
                                                    <?php if($row['status'] == 'Active'): ?>
                                                    <button class="btn btn-sm btn-success complete-btn" data-id="<?php echo $row['trade_id']; ?>">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger cancel-btn" data-id="<?php echo $row['trade_id']; ?>">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php
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
        $('#tradesTable').DataTable({
            "order": [[6, "desc"]]
        });

        // Handle Complete button
        $('.complete-btn').click(function() {
            const tradeId = $(this).data('id');
            Swal.fire({
                title: 'Complete Trade?',
                text: "Are you sure this trade has been completed?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2ecc71',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Yes, complete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateTradeStatus(tradeId, 'Completed');
                }
            });
        });

        // Handle Cancel button
        $('.cancel-btn').click(function() {
            const tradeId = $(this).data('id');
            Swal.fire({
                title: 'Cancel Trade?',
                text: "Are you sure you want to cancel this trade?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateTradeStatus(tradeId, 'Cancelled');
                }
            });
        });

        function updateTradeStatus(tradeId, status) {
            $.ajax({
                url: 'update_trade_status.php',
                type: 'POST',
                data: {
                    trade_id: tradeId,
                    status: status
                },
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: `Trade has been ${status.toLowerCase()}.`,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong.',
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
