<?php
include ('fsession.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['farmer_login_user'])){
    header("location: ../index.php");
} // Redirecting To Home Page
$query4 = "SELECT * from farmerlogin where email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);
$para1 = $row4['farmer_id'];
$para2 = $row4['farmer_name'];
?>
<!DOCTYPE html>
<html>
<head>
    <?php include ('fheader.php'); ?>
    
    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    
    <!-- Critical CSS for immediate visibility -->
    <style>
        /* Prevent FOUC */
        html { 
            visibility: visible; 
            opacity: 1;
            height: 100%;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
        }

        main {
            flex: 1 0 auto;
            display: flex;
            flex-direction: column;
        }
        
        /* Base styles for immediate rendering */
        .section-shaped {
            position: relative;
            overflow: hidden;
            flex: 1;
        }
        
        .bg-gradient-success {
            background: linear-gradient(87deg, #2dce89 0, #2dcecc 100%) !important;
        }
        
        .shape {
            position: absolute;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        .shape-style-1.bg-gradient-success span {
            background: rgba(255,255,255,0.1);
        }
        
        .container {
            position: relative;
            z-index: 2;
        }
        
        .text-white {
            color: #fff !important;
        }
        
        .card {
            background: #fff;
            border-radius: 1rem;
            border: 0;
        }
        
        .form-control {
            opacity: 1 !important;
            color: #525f7f !important;
            background-color: #fff !important;
        }
    </style>
</head>

<body>
    <?php include ('fnav.php'); ?>
    <main>
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
      <span></span>
      <span></span>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="display-3 text-white">Rainfall Prediction</h2>
                    <p class="lead text-white">Predict rainfall for your region using advanced machine learning</p>
                </div>

                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <form role="form" action="#" method="post">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-control-label">
                                        <i class="fas fa-map-marker-alt mr-2 text-success"></i>Region
                                    </label>
                                    <div class="form-group">
                                        <select id="region-select" name="region" class="form-control form-control-lg shadow-none" required>
                                            <option value="">Select Region</option>
                                            <option value="ANDAMAN & NICOBAR ISLANDS">ANDAMAN & NICOBAR ISLANDS</option>
                                            <option value="ARUNACHAL PRADESH">ARUNACHAL PRADESH</option>
                                            <option value="ASSAM & MEGHALAYA">ASSAM & MEGHALAYA</option>
                                            <option value="NAGA MANI MIZO TRIPURA">NAGA MANI MIZO TRIPURA</option>
                                            <option value="SUB HIMALAYAN WEST BENGAL & SIKKIM">SUB HIMALAYAN WEST BENGAL & SIKKIM</option>
                                            <option value="GANGETIC WEST BENGAL">GANGETIC WEST BENGAL</option>
                                            <option value="ORISSA">ORISSA</option>
                                            <option value="JHARKHAND">JHARKHAND</option>
                                            <option value="BIHAR">BIHAR</option>
                                            <option value="EAST UTTAR PRADESH">EAST UTTAR PRADESH</option>
                                            <option value="WEST UTTAR PRADESH">WEST UTTAR PRADESH</option>
                                            <option value="UTTARAKHAND">UTTARAKHAND</option>
                                            <option value="HARYANA DELHI & CHANDIGARH">HARYANA DELHI & CHANDIGARH</option>
                                            <option value="PUNJAB">PUNJAB</option>
                                            <option value="HIMACHAL PRADESH">HIMACHAL PRADESH</option>
                                            <option value="JAMMU & KASHMIR">JAMMU & KASHMIR</option>
                                            <option value="WEST RAJASTHAN">WEST RAJASTHAN</option>
                                            <option value="EAST RAJASTHAN">EAST RAJASTHAN</option>
                                            <option value="WEST MADHYA PRADESH">WEST MADHYA PRADESH</option>
                                            <option value="EAST MADHYA PRADESH">EAST MADHYA PRADESH</option>
                                            <option value="GUJARAT REGION">GUJARAT REGION</option>
                                            <option value="SAURASHTRA & KUTCH">SAURASHTRA & KUTCH</option>
                                            <option value="KONKAN & GOA">KONKAN & GOA</option>
                                            <option value="MADHYA MAHARASHTRA">MADHYA MAHARASHTRA</option>
                                            <option value="MARATHWADA">MARATHWADA</option>
                                            <option value="VIDARBHA">VIDARBHA</option>
                                            <option value="CHHATTISGARH">CHHATTISGARH</option>
                                            <option value="COASTAL ANDHRA PRADESH">COASTAL ANDHRA PRADESH</option>
                                            <option value="TELANGANA">TELANGANA</option>
                                            <option value="RAYALSEEMA">RAYALSEEMA</option>
                                            <option value="TAMIL NADU">TAMIL NADU</option>
                                            <option value="COASTAL KARNATAKA">COASTAL KARNATAKA</option>
                                            <option value="NORTH INTERIOR KARNATAKA">NORTH INTERIOR KARNATAKA</option>
                                            <option value="SOUTH INTERIOR KARNATAKA">SOUTH INTERIOR KARNATAKA</option>
                                            <option value="KERALA">KERALA</option>
                                            <option value="LAKSHADWEEP">LAKSHADWEEP</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-control-label">
                                        <i class="fas fa-calendar-alt mr-2 text-success"></i>Month
                                    </label>
                                    <div class="form-group">
                                        <select id="month-select" name="month" class="form-control form-control-lg shadow-none" required>
                                            <option value="">Select Month</option>
                                            <option value="JAN">January</option>
                                            <option value="FEB">February</option>
                                            <option value="MAR">March</option>
                                            <option value="APR">April</option>
                                            <option value="MAY">May</option>
                                            <option value="JUN">June</option>
                                            <option value="JUL">July</option>
                                            <option value="AUG">August</option>
                                            <option value="SEP">September</option>
                                            <option value="OCT">October</option>
                                            <option value="NOV">November</option>
                                            <option value="DEC">December</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" name="Rainfall_Predict" class="btn btn-lg btn-success btn-icon mb-3 mb-sm-0 w-100">
                                    <span class="btn-inner--icon"><i class="fas fa-cloud-rain mr-2"></i></span>
                                    <span class="btn-inner--text">Predict Rainfall</span>
                                </button>
                            </div>
                        </form>

                        <?php if(isset($_POST['Rainfall_Predict'])): 
                            $region = trim($_POST['region']);
                            $month = trim($_POST['month']);
                            $Jregion = json_encode($region);
                            $Jmonth = json_encode($month);
                            $command = escapeshellcmd("python ML/rainfall_prediction/rainfall_prediction.py $Jregion $Jmonth");
                            ob_start();
                            passthru($command);
                            $output = ob_get_clean();
                        ?>
                            <div class="mt-5">
                                <div class="alert alert-success fade show shadow-lg" role="alert">
                                    <div class="d-flex align-items-center">
                                        <div class="alert-icon mr-3">
                                            <i class="fas fa-cloud-rain fa-2x text-success"></i>
                                        </div>
                                        <div>
                                            <h4 class="alert-heading mb-1 text-success">Prediction Results</h4>
                                            <p class="mb-0 text-sm text-success">
                                                Predicted Rainfall for <strong class="text-success"><?php echo $region; ?></strong> 
                                                in <strong class="text-success"><?php echo $month; ?></strong>:
                                            </p>
                                            <h3 class="mt-2 mb-0 text-success" style="font-size: 2rem;"><?php echo number_format((float)$output, 2); ?> mm</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    </main>
<?php include("../modern-footer.php"); ?>

<!-- Add custom styles -->
<style>
    .form-control {
        font-size: 1rem;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #2dce89;
        box-shadow: 0 0 0 0.2rem rgba(45, 206, 137, 0.25);
    }

    .form-control-label {
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        color: #525f7f;
    }

    .btn-success {
        background: linear-gradient(87deg, #2dce89 0, #2dcecc 100%) !important;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
    }

    .alert-success {
        background: rgba(45, 206, 137, 0.1);
        border: none;
        border-radius: 1rem;
    }

    .text-success {
        color: #2dce89 !important;
    }

    .alert-icon {
        color: #2dce89;
    }

    /* Add transition for smoother color changes */
    .alert * {
        transition: color 0.3s ease;
    }

    .display-3 {
        font-weight: 600;
        line-height: 1.3;
    }

    .lead {
        font-size: 1.25rem;
        font-weight: 300;
    }

    select.form-control {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232dce89'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.5rem;
        padding-right: 3rem;
    }

    select.form-control option {
        color: #525f7f;
    }

    .card {
        border-radius: 1rem;
    }

    .card-body {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 1rem;
    }
</style>
</body>
</html>
