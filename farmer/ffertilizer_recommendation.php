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
    /* Enhance overall visibility */
    body {
        color: #2E4053;
    }

    /* Improve form section */
    .form-control-label {
        color: #2E4053;
        font-weight: 600;
        font-size: 1rem;
    }

    /* Enhance card appearance */
    .card {
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    /* Improve section title */
    .section-title {
        color: #ffffff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        font-weight: 600;
    }

    /* Style the recommendation result section */
    .fertilizer-recommendation {
        background: #ffffff;
        border-radius: 10px;
        padding: 20px;
        margin-top: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .fertilizer-recommendation h2 {
        color: #2E4053;
        font-weight: 600;
        margin-bottom: 20px;
        font-size: 1.5rem;
    }

    .recommendation-result {
        background: linear-gradient(135deg, #2DCE89 0%, #2dce89e6 100%);
        color: #ffffff;
        padding: 15px 20px;
        border-radius: 8px;
        margin: 15px 0;
        font-size: 1.1rem;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(45, 206, 137, 0.2);
    }

    .recommendation-result i {
        color: #ffffff;
        margin-right: 10px;
    }

    .recommendation-info {
        background: #f8f9fa;
        border-left: 4px solid #2DCE89;
        padding: 15px 20px;
        margin: 15px 0;
        color: #2E4053;
        font-size: 1rem;
        line-height: 1.6;
        border-radius: 0 8px 8px 0;
    }

    /* Style the form inputs */
    .input-group {
        margin-bottom: 1.5rem;
    }

    .input-group-text {
        background-color: #f8f9fa;
        color: #2E4053;
        font-weight: 500;
        border: 1px solid #e9ecef;
    }

    /* Style the submit button */
    .btn-get-recommendation {
        background: linear-gradient(135deg, #2DCE89 0%, #2dce89e6 100%);
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(45, 206, 137, 0.3);
        transition: all 0.3s ease;
    }

    .btn-get-recommendation:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(45, 206, 137, 0.4);
    }

    /* Improve select dropdowns */
    select.form-control {
        color: #2E4053;
        font-weight: 500;
        background-color: #ffffff;
        border: 1px solid #e9ecef;
        cursor: pointer;
    }

    select.form-control:focus {
        border-color: #2DCE89;
        box-shadow: 0 0 0 0.2rem rgba(45, 206, 137, 0.25);
    }
</style>

<style>
    .section-shaped .shape {
        opacity: 0.9;
    }
    
    .text-white {
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .text-white-50 {
        color: #ffffff !important;
        opacity: 0.9;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        font-size: 1.1rem;
    }
    
    .card {
        background: rgba(255, 255, 255, 0.98);
    }
    
    .form-control-label {
        color: #2E4053;
        font-weight: 600;
        font-size: 0.95rem;
    }
    
    .text-muted {
        color: #34495E !important;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        color: #2E4053;
        font-weight: 500;
    }
    
    .form-control {
        color: #2E4053;
        font-weight: 500;
    }
    
    .form-control::placeholder {
        color: #95A5A6;
    }
    
    .card-body {
        box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.02);
    }
    
    .display-4 {
        font-weight: 600;
        letter-spacing: -0.5px;
    }

    /* Enhance section background */
    .bg-gradient-success {
        background: linear-gradient(150deg, #2DCE89 0%, #2dce89 100%);
    }
    
    /* Add more contrast to icons */
    .fas {
        color: #2DCE89;
    }
    
    /* Improve input focus states */
    .form-control:focus {
        border-color: #2DCE89;
        box-shadow: 0 0 0 0.2rem rgba(45, 206, 137, 0.25);
    }
</style>

<style>
    .form-control {
        transition: all 0.2s ease;
        border: 1px solid #e9ecef;
    }
    .form-control:focus {
        border-color: #2dce89;
        box-shadow: 0 0 0 0.2rem rgba(45, 206, 137, 0.25);
    }
    .input-group-text {
        border: 1px solid #e9ecef;
        background-color: #f8f9fe;
        color: #8898aa;
    }
    .btn-success {
        background: linear-gradient(87deg, #2dce89 0, #2dcecc 100%);
        border: none;
        transition: all 0.2s ease;
    }
    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
    }
    .alert {
        border: none;
        border-radius: 0.5rem;
    }
    .alert-success {
        background: linear-gradient(87deg, rgba(45, 206, 137, 0.1) 0, rgba(45, 206, 204, 0.1) 100%);
        color: #2dce89;
    }
    .bg-gradient-success {
        background: linear-gradient(150deg, #2dce89 15%, #2dcecc 70%, #1171ef 94%);
    }
    .card {
        border: none;
        transition: all 0.2s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    select.form-control {
        height: calc(2.75rem + 2px);
    }
    .form-control-lg {
        padding: 1rem 1rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.4rem;
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
                            <h2 class="text-white display-4">Fertilizer Recommendation</h2>
                            <p class="text-white-50">Get AI-powered fertilizer recommendations based on soil conditions and crop type</p>
                        </div>

                        <div class="card shadow-lg border-0">
                            <div class="card-body p-4">
                                <form role="form" action="#" method="post">
                                    <!-- Soil Nutrients Section -->
                                    <div class="mb-5">
                                        <h5 class="text-muted mb-4">
                                            <i class="fas fa-flask mr-2"></i>Soil Nutrients
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">Nitrogen (N)</label>
                                                    <div class="input-group">
                                                        <input type="number" name="n" class="form-control" placeholder="e.g., 37" required>
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
                                                        <input type="number" name="p" class="form-control" placeholder="e.g., 0" required>
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
                                                        <input type="number" name="k" class="form-control" placeholder="e.g., 0" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">mg/kg</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Environmental Conditions -->
                                    <div class="mb-5">
                                        <h5 class="text-muted mb-4">
                                            <i class="fas fa-cloud-sun mr-2"></i>Environmental Conditions
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">Temperature</label>
                                                    <div class="input-group">
                                                        <input type="number" name="t" class="form-control" placeholder="e.g., 26" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">°C</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">Humidity</label>
                                                    <div class="input-group">
                                                        <input type="number" name="h" class="form-control" placeholder="e.g., 52" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">Soil Moisture</label>
                                                    <div class="input-group">
                                                        <input type="number" name="soilMoisture" class="form-control" placeholder="e.g., 38" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Crop Information -->
                                    <div class="mb-5">
                                        <h5 class="text-muted mb-4">
                                            <i class="fas fa-seedling mr-2"></i>Crop Information
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">Soil Type</label>
                                                    <select name="soil" class="form-control form-control-lg" required>
                                                        <option value="">Select Soil Type</option>
                                                        <option value="Sandy">Sandy</option>
                                                        <option value="Loamy">Loamy</option>
                                                        <option value="Black">Black</option>
                                                        <option value="Red">Red</option>
                                                        <option value="Clayey">Clayey</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label class="form-control-label">Crop Type</label>
                                                    <select name="crop" class="form-control form-control-lg" required>
                                                        <option value="">Select Crop</option>
                                                        <option value="Maize">Maize</option>
                                                        <option value="Sugarcane">Sugarcane</option>
                                                        <option value="Cotton">Cotton</option>
                                                        <option value="Tobacco">Tobacco</option>
                                                        <option value="Paddy">Paddy</option>
                                                        <option value="Barley">Barley</option>
                                                        <option value="Wheat">Wheat</option>
                                                        <option value="Millets">Millets</option>
                                                        <option value="Oil seeds">Oil seeds</option>
                                                        <option value="Pulses">Pulses</option>
                                                        <option value="Ground Nuts">Ground Nuts</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" name="Fert_Recommend" class="btn btn-lg btn-success btn-icon">
                                            <span class="btn-inner--icon"><i class="fas fa-leaf"></i></span>
                                            <span class="btn-inner--text">Get Recommendation</span>
                                        </button>
                                    </div>
                                </form>

                                <?php if(isset($_POST['Fert_Recommend'])): 
                                    $n = trim($_POST['n']);
                                    $p = trim($_POST['p']);
                                    $k = trim($_POST['k']);
                                    $t = trim($_POST['t']);
                                    $h = trim($_POST['h']);
                                    $sm = trim($_POST['soilMoisture']);
                                    $soil = trim($_POST['soil']);
                                    $crop = trim($_POST['crop']);

                                    $Jsonn = json_encode($n);
                                    $Jsonp = json_encode($p);
                                    $Jsonk = json_encode($k);
                                    $Jsont = json_encode($t);
                                    $Jsonh = json_encode($h);
                                    $Jsonsm = json_encode($sm);
                                    $Jsonsoil = json_encode($soil);
                                    $Jsoncrop = json_encode($crop);

                                    $command = escapeshellcmd("python ML/fertilizer_recommendation/fertilizer_recommendation.py $Jsonn $Jsonp $Jsonk $Jsont $Jsonh $Jsonsm $Jsonsoil $Jsoncrop");
                                    ob_start();
                                    passthru($command);
                                    $output = ob_get_clean();
                                ?>
                                    <div class="mt-5 fertilizer-recommendation">
                                        <div class="alert alert-success fade show" role="alert">
                                            <div class="d-flex align-items-center">
                                                <div class="alert-icon mr-3">
                                                    <i class="fas fa-flask fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h4 class="mb-1">Fertilizer Recommendation</h4>
                                                    <p class="mb-0">Based on your soil conditions and crop type:</p>
                                                    <div class="mt-3 p-3 bg-gradient-success text-white rounded recommendation-result">
                                                        <i class="fas fa-leaf mr-2"></i>
                                                        Recommended Fertilizer: <strong><?php echo $output; ?></strong>
                                                    </div>
                                                    <p class="mt-3 mb-0 text-sm recommendation-info">
                                                        <i class="fas fa-info-circle mr-2"></i>
                                                        This recommendation is based on your soil nutrients, environmental conditions, and crop requirements.
                                                    </p>
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
</body>
</html>
