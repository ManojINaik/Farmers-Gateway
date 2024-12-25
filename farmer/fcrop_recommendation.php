<?php
include ('fsession.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['farmer_login_user'])){
header("location: ../index.php");} // Redirecting To Home Page
$query4 = "SELECT * from farmerlogin where email='$user_check'";
              $ses_sq4 = mysqli_query($conn, $query4);
              $row4 = mysqli_fetch_assoc($ses_sq4);
              $para1 = $row4['farmer_id'];
              $para2 = $row4['farmer_name'];
			  
?>
<!DOCTYPE html>
<html>
<head>
    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    
    <?php include ('fheader.php'); ?>
    
    <style>
        .bg-gradient-success {
            background-color: #28a745;
            background-image: linear-gradient(180deg, #28a745 10%, #28a745 100%);
            background-size: 100% 300px;
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
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <h2 class="text-white display-4">Crop Recommendation</h2>
                    <p class="text-white-50">Get AI-powered crop recommendations based on soil and weather conditions</p>
                </div>

                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <form role="form" action="#" method="post">
                            <div class="row">
                                <!-- Soil Nutrients Section -->
                                <div class="col-12 mb-4">
                                    <h5 class="text-muted mb-3">
                                        <i class="fas fa-leaf mr-2"></i>Soil Nutrients
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Nitrogen (N)</label>
                                                <div class="input-group">
                                                    <input type="number" name="n" class="form-control" placeholder="e.g., 90" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">mg/kg</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Phosphorus (P)</label>
                                                <div class="input-group">
                                                    <input type="number" name="p" class="form-control" placeholder="e.g., 42" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">mg/kg</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Potassium (K)</label>
                                                <div class="input-group">
                                                    <input type="number" name="k" class="form-control" placeholder="e.g., 43" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">mg/kg</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Environmental Conditions Section -->
                                <div class="col-12 mb-4">
                                    <h5 class="text-muted mb-3">
                                        <i class="fas fa-cloud-sun mr-2"></i>Environmental Conditions
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Temperature</label>
                                                <div class="input-group">
                                                    <input type="number" name="t" step="0.01" class="form-control" placeholder="e.g., 21" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">°C</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Humidity</label>
                                                <div class="input-group">
                                                    <input type="number" name="h" step="0.01" class="form-control" placeholder="e.g., 82" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Soil Properties Section -->
                                <div class="col-12">
                                    <h5 class="text-muted mb-3">
                                        <i class="fas fa-mountain mr-2"></i>Soil Properties
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-control-label">pH Level</label>
                                                <div class="input-group">
                                                    <input type="number" name="ph" step="0.01" class="form-control" placeholder="e.g., 6.5" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Rainfall</label>
                                                <div class="input-group">
                                                    <input type="number" name="r" step="0.01" class="form-control" placeholder="e.g., 203" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">mm</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" name="Crop_Recommend" class="btn btn-lg btn-success btn-icon">
                                    <span class="btn-inner--icon"><i class="fas fa-seedling"></i></span>
                                    <span class="btn-inner--text">Get Recommendation</span>
                                </button>
                            </div>
                        </form>

                        <?php if(isset($_POST['Crop_Recommend'])): 
                            $n = trim($_POST['n']);
                            $p = trim($_POST['p']);
                            $k = trim($_POST['k']);
                            $t = trim($_POST['t']);
                            $h = trim($_POST['h']);
                            $ph = trim($_POST['ph']);
                            $r = trim($_POST['r']);

                            $Jsonn = json_encode($n);
                            $Jsonp = json_encode($p);
                            $Jsonk = json_encode($k);
                            $Jsont = json_encode($t);
                            $Jsonh = json_encode($h);
                            $Jsonph = json_encode($ph);
                            $Jsonr = json_encode($r);

                            $command = escapeshellcmd("python ML/crop_recommendation/recommend.py $Jsonn $Jsonp $Jsonk $Jsont $Jsonh $Jsonph $Jsonr");
                            ob_start();
                            passthru($command);
                            $output = ob_get_clean();
                        ?>
                            <div class="mt-5">
                                <div class="alert alert-success fade show" role="alert">
                                    <div class="d-flex align-items-center">
                                        <div class="alert-icon mr-3">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-1">Recommendation Results</h4>
                                            <p class="mb-0">Based on your soil conditions and environmental factors:</p>
                                            <div class="mt-3 p-3 bg-gradient-success text-white rounded">
                                                <i class="fas fa-seedling mr-2"></i>
                                                Recommended Crop: <strong><?php echo $output; ?></strong>
                                            </div>
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
    </div>
</section>
</main>
<?php include("../modern-footer.php"); ?>
</body>
</html>
