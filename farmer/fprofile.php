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
              $para3 = $row4['password'];
			  $para5 = $row4['email'];
			  $para6 = $row4['phone_no'];
			  $para7 = $row4['F_gender'];
			  $para8 = $row4['F_birthday'];
			  $para9 = $row4['F_State'];
			  $para10 = $row4['F_District'];
			  $para11 = $row4['F_Location'];

if(isset($_POST['farmerupdate'])) {
    $id = ($_POST['id']);
    $name = ($_POST['name']);
    $email = ($_POST['email']);
    $mobile = ($_POST['mobile']);
    $gender = ($_POST['gender']);
    $dob = ($_POST['dob']);
    $state = ($_POST['state']);
    $district = ($_POST['district']);		
    $city = ($_POST['city']);
    $pass = ($_POST['pass']);

    $query5 = "SELECT StateName from state where StCode ='$state'";
    $ses_sq5 = mysqli_query($conn, $query5);
    $row5 = mysqli_fetch_assoc($ses_sq5);
    $statename = $row5['StateName'];
			  
    $updatequery1 = "UPDATE farmerlogin set farmer_name='$name', email='$email', phone_no='$mobile', F_gender='$gender', F_birthday='$dob', F_State='$statename', F_District='$district', F_Location='$city', password='$pass' where farmer_id='$id'";
    mysqli_query($conn, $updatequery1);
    header("location: fprofile.php");
}		  
?>

<!DOCTYPE html>
<html>
<?php include ('fheader.php');  ?>

<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .profile-header {
            background: linear-gradient(135deg, #43a047, #1b5e20);
            padding: 2rem;
            text-align: center;
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            margin-bottom: 1rem;
        }
        .profile-info {
            padding: 1.5rem;
        }
        .info-item {
            padding: 0.8rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #666;
            font-weight: 600;
            width: 140px;
        }
        .info-value {
            color: #333;
            flex: 1;
            text-align: right;
        }
        .edit-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            border-radius: 25px;
            padding: 8px 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .edit-btn:hover {
            background: white;
            color: #43a047;
        }
        .modal-content {
            border-radius: 15px;
        }
        .modal-header {
            background: linear-gradient(135deg, #43a047, #1b5e20);
            color: white;
            border: none;
            padding: 1rem 1.5rem;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 8px 12px;
        }
        .form-control:focus {
            border-color: #43a047;
            box-shadow: none;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
    </style>

    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</head>

<body class="bg-light">
    <?php include ('fnav.php'); ?>
    
    <div class="container">
        <div class="profile-card">
            <div class="profile-header">
                <img src="../assets/img/agri.png" alt="Profile" class="profile-img">
                <h3 class="mb-1"><?php echo $para2 ?></h3>
                <p class="mb-3"><?php echo $para5 ?></p>
                <button data-toggle="modal" data-target="#edit" class="edit-btn">
                    <i class="fas fa-edit mr-2"></i>Edit Profile
                </button>
            </div>

            <div class="profile-info">
                <div class="info-item">
                    <div class="info-label">Farmer ID</div>
                    <div class="info-value"><?php echo $para1 ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Mobile Number</div>
                    <div class="info-value"><?php echo $para6 ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Gender</div>
                    <div class="info-value"><?php echo $para7 ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date of Birth</div>
                    <div class="info-value"><?php echo $para8 ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Location</div>
                    <div class="info-value"><?php echo $para11 ?>, <?php echo $para10 ?>, <?php echo $para9 ?></div>
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
                    <form method="POST" autocomplete="new-password" class="p-4">
                        <div class="form-group">
                            <label>Farmer ID</label>
                            <input name="id" class="form-control" value="<?php echo $para1 ?>" readonly />
                        </div>

                        <div class="form-group">
                            <label>Farmer Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $para2 ?>" />
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $para5 ?>" readonly />
                        </div>

                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="number" name="mobile" class="form-control" value="<?php echo $para6 ?>" />
                        </div>

                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option selected hidden><?php echo $para7 ?></option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="<?php echo $para8 ?>" />
                        </div>

                        <div class="form-group">
                            <label>State</label>
                            <select name="state" id="state" class="form-control" onChange="getdistrict(this.value);">
                                <option value=""><?php echo $para9 ?></option>
                                <?php 
                                $query = mysqli_query($conn, "SELECT * FROM state");
                                while($row = mysqli_fetch_array($query)) { 
                                ?>
                                <option value="<?php echo $row['StCode']; ?>"><?php echo $row['StateName']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>District</label>
                            <select name="district" id="district-list" class="form-control">
                                <option value=""><?php echo $para10 ?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" value="<?php echo $para11 ?>" />
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <div class="input-group">
                                <input name="pass" type="password" value="<?php echo $para3 ?>" class="form-control" id="password" />
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="password_show_hide();">
                                        <i class="fas fa-eye" id="show_eye"></i>
                                        <i class="fas fa-eye-slash d-none" id="hide_eye"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="farmerupdate" class="btn btn-success btn-block">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php  ?>

    <script>
        function password_show_hide() {
            var x = document.getElementById("password");
            var show_eye = document.getElementById("show_eye");
            var hide_eye = document.getElementById("hide_eye");
            hide_eye.classList.remove("d-none");
            if (x.type === "password") {
                x.type = "text";
                show_eye.style.display = "none";
                hide_eye.style.display = "block";
            } else {
                x.type = "password";
                show_eye.style.display = "block";
                hide_eye.style.display = "none";
            }
        }

        function getdistrict(val) {
            $.ajax({
                type: "POST",
                url: "fget_district.php",
                data: 'state_id=' + val,
                success: function(data) {
                    $("#district-list").html(data);
                }
            });
        }
    </script>
    <?php include("../modern-footer.php"); ?>
</body>
</html>
