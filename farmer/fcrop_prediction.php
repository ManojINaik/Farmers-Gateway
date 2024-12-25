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
<head>
    <?php include ('fheader.php');  ?>
    
    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .prediction-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .prediction-card:hover {
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
            margin-bottom: 10px;
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
        .predict-btn {
            background: linear-gradient(135deg, #43a047, #1b5e20);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        .predict-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .result-card {
            background: linear-gradient(135deg, #fff, #f8f9fa);
            border-radius: 15px;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .result-text {
            color: #2d3436;
            font-size: 1.1rem;
            line-height: 1.6;
            margin: 15px 0;
        }
        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #43a047;
        }
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 0.5rem;
            display: block;
        }
        .season-icon {
            margin-right: 8px;
            color: #43a047;
        }
    </style>
</head>

<body class="bg-white" id="top">
    <?php include ('fnav.php'); ?>
 	
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
                    <div class="prediction-card">
                        <div class="card-header-custom">
                            <h2 class="mb-0">
                                <i class="fas fa-seedling mr-2"></i>
                                Crop Prediction
                            </h2>
                            <p class="text-white-50 mb-0">Get intelligent crop recommendations based on your location and season</p>
                        </div>

                        <div class="card-body p-4">
                            <form role="form" action="#" method="post">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-map-marker-alt mr-2"></i>
                                                State
                                            </label>
                                            <select onchange="print_city('state', this.selectedIndex);" id="sts" name="stt" class="form-control custom-select" required>
                                                <option value="">Select State...</option>
                                            </select>
                                            <script language="javascript">print_state("sts");</script>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-city mr-2"></i>
                                                District
                                            </label>
                                            <select id="state" name="district" class="form-control custom-select" required>
                                                <option value="">Select District...</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-cloud-sun mr-2"></i>
                                                Season
                                            </label>
                                            <select name="Season" class="form-control custom-select" required>
                                                <option value="">Select Season...</option>
                                                <option value="Kharif">
                                                    <i class="fas fa-sun season-icon"></i>Kharif (Monsoon)
                                                </option>
                                                <option value="Whole Year">
                                                    <i class="fas fa-calendar-alt season-icon"></i>Whole Year
                                                </option>
                                                <option value="Autumn">
                                                    <i class="fas fa-leaf season-icon"></i>Autumn
                                                </option>
                                                <option value="Rabi">
                                                    <i class="fas fa-snowflake season-icon"></i>Rabi (Winter)
                                                </option>
                                                <option value="Summer">
                                                    <i class="fas fa-sun season-icon"></i>Summer
                                                </option>
                                                <option value="Winter">
                                                    <i class="fas fa-snowflake season-icon"></i>Winter
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" name="Crop_Predict" class="predict-btn">
                                        <i class="fas fa-magic mr-2"></i>Predict Suitable Crops
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if(isset($_POST['Crop_Predict'])): ?>
                    <div class="result-card">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-chart-bar fa-2x text-success mr-3"></i>
                            <h3 class="mb-0">Prediction Results</h3>
                        </div>
                        <div class="result-text">
                            <?php 
                            $state = trim($_POST['stt']);
                            $district = trim($_POST['district']);
                            $season = trim($_POST['Season']);

                            echo "<p class='mb-3'><i class='fas fa-map-marker-alt text-success mr-2'></i>Location: <strong>$district</strong></p>";
                            echo "<p class='mb-3'><i class='fas fa-cloud-sun text-success mr-2'></i>Season: <strong>$season</strong></p>";
                            echo "<p class='mb-3'><i class='fas fa-seedling text-success mr-2'></i>Recommended Crops:</p>";

                            $JsonState = json_encode($state);
                            $JsonDistrict = json_encode($district);
                            $JsonSeason = json_encode($season);
                            
                            $command = escapeshellcmd("python ML/crop_prediction/ZDecision_Tree_Model_Call.py $JsonState $JsonDistrict $JsonSeason");
                            $output = passthru($command);
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    </main>
    <?php include("../modern-footer.php"); ?>
</body>
</html>
