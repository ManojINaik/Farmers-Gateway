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
<?php include ('fheader.php');  ?>
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .trade-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .trade-card:hover {
            transform: translateY(-5px);
        }
        .card-header-custom {
            background: linear-gradient(135deg, #43a047, #1b5e20);
            padding: 20px;
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
        .custom-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }
        .submit-btn {
            background: linear-gradient(135deg, #43a047, #1b5e20);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .price-alert {
            background: linear-gradient(135deg, #fff, #f8f9fa);
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .price-alert strong {
            color: #2d3436;
        }
        .crop-icon {
            font-size: 24px;
            margin-right: 10px;
            color: #43a047;
        }
        .table th {
            background: #f8f9fa;
            color: #2d3436;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }
        .table td {
            vertical-align: middle;
        }
        .success-alert {
            background: linear-gradient(135deg, #43a047, #1b5e20);
            color: white;
            border: none;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-white" id="top">
    <?php include ('fnav.php');  ?>
 	
    <section class="section section-shaped section-lg">
        <div class="shape shape-style-1 bg-gradient-success">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="trade-card">
                        <div class="card-header-custom">
                            <h2 class="mb-0">
                                <i class="fas fa-leaf mr-2"></i>
                                Update Crop Stock
                            </h2>
                            <p class="text-white-50 mb-0">Add your crop details and get current market prices</p>
                        </div>

                        <div class="card-body p-4">
                            <!-- Price Alert -->
                            <div class="alert price-alert alert-dismissible fade show mb-4" style="display: none;" id="popup" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-line crop-icon"></i>
                                    <div>
                                        <strong>Current Market Price</strong>
                                        <p class="mb-0">Average price for <span id="crop" class="font-weight-bold text-success"></span> is: 
                                        <span id="price" class="font-weight-bold text-success"></span></p>
                                    </div>
                                </div>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <!-- Success Alert -->
                            <div class="alert success-alert alert-dismissible fade show mb-4" style="display: none;" id="details" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <strong>Crop Details Added Successfully</strong>
                                </div>
                                <button type="button" class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <form role="form" onsubmit="return tradecrops()" id="sellcrops" action="ftradecropsScript.php" method="POST">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Select Crop</label>
                                            <select id="crops" name="crops" class="form-control custom-select">
                                                <option value="">Choose a crop</option>
                                                <option value="arhar">Arhar</option>
                                                <option value="bajra">Bajra</option>
                                                <option value="barley">Barley</option>
                                                <option value="cotton">Cotton</option>
                                                <option value="gram">Gram</option>
                                                <option value="jowar">Jowar</option>
                                                <option value="jute">Jute</option>
                                                <option value="lentil">Lentil</option>
                                                <option value="maize">Maize</option>
                                                <option value="moong">Moong</option>
                                                <option value="ragi">Ragi</option>
                                                <option value="rice">Rice</option>
                                                <option value="soyabean">Soyabean</option>
                                                <option value="urad">Urad</option>
                                                <option value="wheat">Wheat</option>
                                                <option value="other">Other Crop</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div id="custom-crop-container" style="display:none;" class="col-md-4 mt-2">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-seedling"></i></span>
                                            </div>
                                            <input type="text" name="custom_crop" id="custom-crop" class="form-control" placeholder="Enter your crop name">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Quantity (KG)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-weight-hanging"></i></span>
                                                </div>
                                                <input type="number" name="trade_farmer_cropquantity" class="form-control" placeholder="Enter quantity" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Cost per KG (₹)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-rupee-sign"></i></span>
                                                </div>
                                                <input type="number" name="trade_farmer_cost" id="trade_farmer_cost" class="form-control" placeholder="Enter cost" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" name="Crop_submit" class="submit-btn">
                                        <i class="fas fa-plus-circle mr-2"></i>Add Crop Details
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require("../modern-footer.php"); ?>
    
    <script>
        document.getElementById("crops").addEventListener("change", function() {
            var crops = jQuery('#crops').val();
            
            if(crops == "other"){
                document.getElementById("custom-crop-container").style.display = "block";
                document.getElementById("custom-crop").setAttribute("required", "required");
            } else {
                document.getElementById("custom-crop-container").style.display = "none";
                document.getElementById("custom-crop").removeAttribute("required");
            }

            jQuery.ajax({
                url: 'fcheck_price.php',
                type: 'post',
                data: {
                    crop: crops
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if(data.status == 'success') {
                        document.getElementById('popup').style.display = 'block';
                        document.getElementById('crop').innerHTML = data.crop;
                        document.getElementById('price').innerHTML = '₹ ' + data.price + ' per KG';
                    } else {
                        document.getElementById('popup').style.display = 'none';
                    }
                }
            });
        });

        function tradecrops() {
            var crops = jQuery('#crops').val();
            var customCrop = jQuery('#custom-crop').val();
            
            if(crops == 'other' && (!customCrop || customCrop.trim() === '')) {
                alert('Please enter a crop name for the "Other" option');
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>
