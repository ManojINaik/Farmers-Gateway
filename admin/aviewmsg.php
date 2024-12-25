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
        .message-stats {
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
            color: #3498db;
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
        
        .message-header {
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .message-header h2 {
            color: #2d3436;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .message-header p {
            color: #636e72;
            font-size: 1.1rem;
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
        
        .table thead th {
            border-top: none;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: #2d3436;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }
        
        .table td {
            vertical-align: middle;
            color: #636e72;
            font-size: 0.95rem;
        }
        
        .message-preview {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
        
        .contact-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            background: #E3F2FD;
            color: #1565C0;
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
        
        @media (max-width: 768px) {
            .stat-card {
                margin-bottom: 1rem;
            }
            
            .message-preview {
                max-width: 150px;
            }
        }
    </style>
</head>

<body class="bg-light">
    <?php require ('anav.php'); ?>

    <div class="container py-5">
        <div class="message-header">
            <h2>Contact Messages</h2>
            <p>View and manage contact queries from users</p>
        </div>

        <!-- Statistics Row -->
        <div class="row message-stats">
            <?php
            $total_messages = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM contactus"));
            $unique_contacts = mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT c_email FROM contactus"));
            $recent_messages = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM contactus WHERE c_id > (SELECT MAX(c_id) - 10 FROM contactus)"));
            ?>
            <div class="col-md-4 mb-4">
                <div class="stat-card text-center">
                    <i class="fas fa-envelope stat-icon"></i>
                    <div class="stat-value"><?php echo $total_messages; ?></div>
                    <div class="stat-label">Total Messages</div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="stat-card text-center">
                    <i class="fas fa-users stat-icon"></i>
                    <div class="stat-value"><?php echo $unique_contacts; ?></div>
                    <div class="stat-label">Unique Contacts</div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="stat-card text-center">
                    <i class="fas fa-clock stat-icon"></i>
                    <div class="stat-value"><?php echo $recent_messages; ?></div>
                    <div class="stat-label">Recent Messages</div>
                </div>
            </div>
        </div>

        <!-- Messages Table -->
        <div class="table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Message List</h4>
                <button class="btn btn-primary" onclick="exportTableToExcel()">
                    <i class="fas fa-file-excel mr-2"></i>Export to Excel
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Contact Info</th>
                            <th>Location</th>
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $q = "SELECT * FROM contactus ORDER BY c_id DESC";
                        $query = mysqli_query($conn, $q);
                        while($res = mysqli_fetch_array($query)) {
                        ?>
                        <tr>
                            <td><?php echo $res['c_id']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($res['c_name']); ?>&background=random" 
                                         alt="<?php echo $res['c_name']; ?>" 
                                         class="rounded-circle mr-2" 
                                         style="width: 30px; height: 30px;">
                                    <div>
                                        <div class="font-weight-bold"><?php echo $res['c_name']; ?></div>
                                        <div class="small text-muted">
                                            <i class="fas fa-phone mr-1"></i><?php echo $res['c_mobile']; ?>
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fas fa-envelope mr-1"></i><?php echo $res['c_email']; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="contact-badge">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <?php echo $res['c_address']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="message-preview" 
                                     onclick="showFullMessage('<?php echo addslashes($res['c_message']); ?>', '<?php echo addslashes($res['c_name']); ?>')">
                                    <?php echo $res['c_message']; ?>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-danger btn-delete" 
                                        onclick="confirmDelete(<?php echo $res['c_id']; ?>)">
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
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                "pageLength": 10,
                "order": [[0, "desc"]],
                "language": {
                    "search": "Search messages:",
                    "lengthMenu": "Show _MENU_ messages per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ messages"
                }
            });
        });

        function showFullMessage(message, name) {
            Swal.fire({
                title: `Message from ${name}`,
                text: message,
                icon: 'info',
                confirmButtonColor: '#3498db'
            });
        }

        function confirmDelete(messageId) {
            Swal.fire({
                title: 'Delete Message?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'amsgdelete.php?id=' + messageId;
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
            downloadLink.download = 'contact_messages.xls';
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
</body>
</html>