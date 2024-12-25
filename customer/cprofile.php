<?php
include ('csession.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['customer_login_user'])){
header("location: ../index.php");} // Redirecting To Home Page
$query4 = "SELECT * from custlogin where email='$user_check'";
              $ses_sq4 = mysqli_query($conn, $query4);
              $row4 = mysqli_fetch_assoc($ses_sq4);
              $para1 = $row4['cust_id'];
              $para2 = $row4['cust_name'];
              $para3 = $row4['password'];
			  $para5 = $row4['email'];
			  $para6 = $row4['phone_no'];
			  $para7 = $row4['state'];
			  $para8 = $row4['city'];
			  $para9 = $row4['address'];
			  $para10 = $row4['pincode'];

if(isset($_POST['custupdate'])) {
	$id = ($_POST['id']);
	$name = ($_POST['name']);
	$email = ($_POST['email']);
	$mobile = ($_POST['mobile']);
	$state = ($_POST['state']);
	$city = ($_POST['city']);
	$address = ($_POST['address']);
	$pincode = ($_POST['pincode']);
	$pass = ($_POST['pass']);

    $query5 = "SELECT StateName from state where StCode ='$state'";
	$ses_sq5 = mysqli_query($conn, $query5);
    $row5 = mysqli_fetch_assoc($ses_sq5);
    $statename = $row5['StateName'];
			  
    $updatequery1 = "UPDATE custlogin set cust_name='$name', email='$email', phone_no='$mobile', state='$statename', city='$city', address='$address', pincode='$pincode', password='$pass' where cust_id='$id'";
    mysqli_query($conn, $updatequery1);
	header("location: cprofile.php");
}			  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include ('cheader.php');  ?>
    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <style>
        .profile-section {
            background: linear-gradient(150deg, #8E44AD 15%, #2980B9 70%, #3498DB 94%);
            min-height: 100vh;
            padding: 2rem 0;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .profile-header {
            background: linear-gradient(90deg, #8E44AD, #2980B9);
            padding: 2rem;
            color: white;
            text-align: center;
            position: relative;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            margin: 0 auto 1rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .profile-name {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .profile-role {
            font-size: 1rem;
            opacity: 0.9;
        }

        .profile-body {
            padding: 2rem;
        }

        .info-group {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding-bottom: 1rem;
        }

        .info-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .info-value {
            color: #34495e;
            font-size: 1.1rem;
        }

        .edit-button {
            background: linear-gradient(90deg, #8E44AD, #2980B9);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .edit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.3);
            background: linear-gradient(90deg, #2980B9, #8E44AD);
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(90deg, #8E44AD, #2980B9);
            color: white;
            border: none;
        }

        .modal-body {
            padding: 2rem;
        }

        .form-control {
            border: 2px solid #eee;
            border-radius: 8px;
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .password-toggle {
            cursor: pointer;
            padding: 0.8rem;
            color: #7f8c8d;
        }

        .submit-btn {
            background: linear-gradient(90deg, #8E44AD, #2980B9);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .submit-btn:hover {
            background: linear-gradient(90deg, #2980B9, #8E44AD);
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-card {
                margin: 1rem;
            }
            
            .profile-avatar {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>

<body class="bg-white" id="top">
    <?php include ('cnav.php');  ?>

    <div class="profile-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-5">
                    <div class="profile-card">
                        <div class="profile-header">
                            <img src="../assets/img/customers.png" alt="Profile Picture" class="profile-avatar">
                            <h2 class="profile-name"><?php echo $para2 ?></h2>
                            <p class="profile-role">Customer</p>
                            <button class="edit-button mt-3" data-toggle="modal" data-target="#edit">
                                <i class="fas fa-edit mr-2"></i>Edit Profile
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-md-7">
                    <div class="profile-card">
                        <div class="profile-body">
                            <div class="info-group">
                                <div class="info-label">Customer ID</div>
                                <div class="info-value"><?php echo $para1 ?></div>
                            </div>

                            <div class="info-group">
                                <div class="info-label">Email Address</div>
                                <div class="info-value"><?php echo $para5 ?></div>
                            </div>

                            <div class="info-group">
                                <div class="info-label">Mobile Number</div>
                                <div class="info-value"><?php echo $para6 ?></div>
                            </div>

                            <div class="info-group">
                                <div class="info-label">Location</div>
                                <div class="info-value">
                                    <?php echo $para8 ?>, <?php echo $para7 ?> - <?php echo $para10 ?>
                                </div>
                            </div>

                            <div class="info-group">
                                <div class="info-label">Address</div>
                                <div class="info-value"><?php echo $para9 ?></div>
                            </div>

                            <div class="info-group">
                                <div class="info-label">Password</div>
                                <div class="info-value">••••••••</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="edit" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Profile</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" autocomplete="off">
                        <div class="form-group">
                            <label>Customer ID</label>
                            <input name="id" class="form-control" value="<?php echo $para1 ?>" readonly />
                        </div>

                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $para2 ?>" required />
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $para5 ?>" readonly />
                        </div>

                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="tel" name="mobile" class="form-control" value="<?php echo $para6 ?>" required 
                                   pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number" />
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>State</label>
                                    <select name="state" id="state" class="form-control" onChange="getdistrict(this.value);" required>
                                        <option value=""><?php echo $para7 ?></option>
                                        <?php 
                                        $query = mysqli_query($conn, "SELECT * FROM state");
                                        while($row = mysqli_fetch_array($query)) { 
                                        ?>
                                        <option value="<?php echo $row['StCode']; ?>">
                                            <?php echo $row['StateName']; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>City</label>
                                    <select name="city" id="district-list" class="form-control" required>
                                        <option value=""><?php echo $para8 ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control" rows="3" required><?php echo $para9 ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Pincode</label>
                            <input type="text" name="pincode" class="form-control" value="<?php echo $para10 ?>" 
                                   pattern="[0-9]{6}" title="Please enter a valid 6-digit pincode" required />
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <div class="input-group">
                                <input type="password" name="pass" class="form-control" id="password" 
                                       value="<?php echo $para3 ?>" required />
                                <div class="input-group-append">
                                    <span class="input-group-text password-toggle" onclick="password_show_hide()">
                                        <i class="fas fa-eye" id="show_eye"></i>
                                        <i class="fas fa-eye-slash d-none" id="hide_eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="custupdate" class="submit-btn">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include("../modern-footer.php"); ?>
</body>
</html>