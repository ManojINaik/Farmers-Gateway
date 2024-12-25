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
<?php include ('fheader.php'); ?>
<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .yield-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .yield-card:hover {
            transform: translateY(-5px);
        }
        .card-header-custom {
            background: linear-gradient(135deg, #43a047, #1b5e20);
            padding: 25px;
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
            padding: 25px;
            margin-top: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .result-text {
            color: #2d3436;
            font-size: 1.2rem;
            line-height: 1.6;
            margin: 15px 0;
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
        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #43a047;
        }
        .info-icon {
            color: #43a047;
            margin-right: 8px;
        }
        .yield-value {
            font-size: 2rem;
            color: #43a047;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .area-input {
            position: relative;
        }
        .area-input::after {
            content: 'ha';
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-weight: 600;
        }
    </style>

    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
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
                    <div class="yield-card">
                        <div class="card-header-custom">
                            <h2 class="mb-0">
                                <i class="fas fa-chart-line mr-2"></i>
                                Crop Yield Prediction
                            </h2>
                            <p class="text-white-50 mb-0">Get accurate yield predictions based on your location and crop details</p>
                        </div>

                        <div class="card-body p-4">
                            <form role="form" action="#" method="post">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-map-marker-alt info-icon"></i>
                                                State
                                            </label>
                                            <select name="state" id="state" class="form-control custom-select" required onchange="updateDistricts()">
                                                <option value="">Select a state</option>
                                                <option value="Andhra Pradesh">Andhra Pradesh</option>
                                                <option value="Gujarat">Gujarat</option>
                                                <option value="Haryana">Haryana</option>
                                                <option value="Karnataka">Karnataka</option>
                                                <option value="Madhya Pradesh">Madhya Pradesh</option>
                                                <option value="Maharashtra">Maharashtra</option>
                                                <option value="Punjab">Punjab</option>
                                                <option value="Tamil Nadu">Tamil Nadu</option>
                                                <option value="Telangana">Telangana</option>
                                                <option value="Uttar Pradesh">Uttar Pradesh</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-city info-icon"></i>
                                                District
                                            </label>
                                            <select id="district" name="district" class="form-control custom-select" required>
                                                <option value="">Select a district</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-cloud-sun info-icon"></i>
                                                Season
                                            </label>
                                            <select name="season" id="season" class="form-control custom-select" required>
                                                <option value="">Select a season</option>
                                                <option value="Kharif">Kharif (Monsoon)</option>
                                                <option value="Rabi">Rabi (Winter)</option>
                                                <option value="Summer">Summer</option>
                                                <option value="Whole Year">Whole Year</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-seedling info-icon"></i>
                                                Crop
                                            </label>
                                            <select id="crop" name="crops" class="form-control custom-select" required>
                                                <option value="">Select crop</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-ruler-combined info-icon"></i>
                                                Area
                                            </label>
                                            <div class="area-input">
                                                <input type="number" step="0.01" name="area" class="form-control" placeholder="Enter area" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" name="Yield_Predict" class="predict-btn">
                                        <i class="fas fa-calculator mr-2"></i>Calculate Yield Prediction
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if(isset($_POST['Yield_Predict'])): ?>
                    <div class="result-card">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-chart-bar fa-2x text-success mr-3"></i>
                            <h3 class="mb-0">Prediction Results</h3>
                        </div>
                        <div class="result-text">
                            <?php 
                            $state = trim($_POST['state']);
                            $district = trim($_POST['district']);
                            $season = trim($_POST['season']);
                            $crops = trim($_POST['crops']);
                            $area = trim($_POST['area']);

                            echo "<div class='row mb-4'>";
                            echo "<div class='col-md-4'><p><i class='fas fa-map-marker-alt text-success mr-2'></i>Location: <strong>$district</strong></p></div>";
                            echo "<div class='col-md-4'><p><i class='fas fa-seedling text-success mr-2'></i>Crop: <strong>$crops</strong></p></div>";
                            echo "<div class='col-md-4'><p><i class='fas fa-ruler-combined text-success mr-2'></i>Area: <strong>$area ha</strong></p></div>";
                            echo "</div>";

                            echo "<div class='text-center'><p class='mb-2'>Predicted Yield:</p>";
                            
                            $Jstate = json_encode($state);
                            $Jdistrict = json_encode($district);
                            $Jseason = json_encode($season);
                            $Jcrops = json_encode($crops);
                            $Jarea = json_encode($area);

                            $command = escapeshellcmd("python ML/yield_prediction/yield_prediction.py $Jstate $Jdistrict $Jseason $Jcrops $Jarea");
                            echo "<div class='yield-value'>";
                            $output = passthru($command);
                            echo " Quintals</div>";
                            echo "</div>";
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php require("../modern-footer.php"); ?>
    
    <script>
        const districtsByState = {
            'Andhra Pradesh': ['Anantapur', 'Chittoor', 'East Godavari', 'Guntur', 'Krishna', 'Kurnool', 'Prakasam', 'Srikakulam', 'Visakhapatnam', 'Vizianagaram', 'West Godavari', 'YSR Kadapa'],
            'Gujarat': ['Ahmedabad', 'Amreli', 'Anand', 'Banaskantha', 'Bharuch', 'Bhavnagar', 'Dahod', 'Gandhinagar', 'Jamnagar', 'Junagadh', 'Kheda', 'Kutch', 'Mehsana', 'Panchmahal', 'Rajkot', 'Sabarkantha', 'Surat', 'Vadodara'],
            'Haryana': ['Ambala', 'Bhiwani', 'Faridabad', 'Fatehabad', 'Hisar', 'Jhajjar', 'Jind', 'Kaithal', 'Karnal', 'Kurukshetra', 'Mahendragarh', 'Palwal', 'Panchkula', 'Panipat', 'Rewari', 'Rohtak', 'Sirsa', 'Sonipat', 'Yamunanagar'],
            'Karnataka': ['BAGALKOT', 'BANGALORE_RURAL', 'BELGAUM', 'BELLARY', 'BENGALURU_URBAN', 'BIDAR', 'BIJAPUR', 'CHAMARAJANAGAR', 'CHIKBALLAPUR', 'CHIKMAGALUR', 'CHITRADURGA', 'DAKSHIN_KANNAD', 'DAVANGERE', 'DHARWAD', 'GADAG', 'GULBARGA', 'HAVERI', 'HASSAN', 'KODAGU', 'KOLAR', 'KOPPAL', 'MANDYA', 'MYSORE', 'RAICHUR', 'RAMANAGARA', 'SHIMOGA', 'TUMKUR', 'UDUPI', 'UTTAR_KANNAD', 'YADGIR'],
            'Madhya Pradesh': ['Bhopal', 'Indore', 'Jabalpur', 'Gwalior', 'Ujjain', 'Sagar', 'Dewas', 'Satna', 'Ratlam', 'Rewa', 'Mandsaur', 'Bhind', 'Morena', 'Vidisha', 'Chhindwara', 'Damoh', 'Datia', 'Hoshangabad'],
            'Maharashtra': ['Ahmednagar', 'Akola', 'Amravati', 'Aurangabad', 'Beed', 'Bhandara', 'Buldhana', 'Chandrapur', 'Dhule', 'Gadchiroli', 'Gondia', 'Jalgaon', 'Jalna', 'Kolhapur', 'Latur', 'Mumbai City', 'Mumbai Suburban', 'Nagpur', 'Nanded', 'Nashik', 'Osmanabad', 'Palghar', 'Parbhani', 'Pune', 'Raigad', 'Ratnagiri', 'Sangli', 'Satara', 'Sindhudurg', 'Solapur', 'Thane', 'Wardha', 'Washim', 'Yavatmal'],
            'Punjab': ['Amritsar', 'Barnala', 'Bathinda', 'Faridkot', 'Fatehgarh Sahib', 'Fazilka', 'Ferozepur', 'Gurdaspur', 'Hoshiarpur', 'Jalandhar', 'Kapurthala', 'Ludhiana', 'Mansa', 'Moga', 'Muktsar', 'Nawanshahr', 'Pathankot', 'Patiala', 'Rupnagar', 'Sangrur', 'SAS Nagar', 'Tarn Taran'],
            'Tamil Nadu': ['Ariyalur', 'Chennai', 'Coimbatore', 'Cuddalore', 'Dharmapuri', 'Dindigul', 'Erode', 'Kanchipuram', 'Kanyakumari', 'Karur', 'Krishnagiri', 'Madurai', 'Nagapattinam', 'Namakkal', 'Nilgiris', 'Perambalur', 'Pudukkottai', 'Ramanathapuram', 'Salem', 'Sivaganga', 'Thanjavur', 'Theni', 'Thoothukudi', 'Tiruchirappalli', 'Tirunelveli', 'Tiruppur', 'Tiruvallur', 'Tiruvannamalai', 'Tiruvarur', 'Vellore', 'Viluppuram', 'Virudhunagar'],
            'Telangana': ['Adilabad', 'Bhadradri Kothagudem', 'Hyderabad', 'Jagtial', 'Jangaon', 'Jayashankar Bhupalpally', 'Jogulamba Gadwal', 'Kamareddy', 'Karimnagar', 'Khammam', 'Kumuram Bheem', 'Mahabubabad', 'Mahabubnagar', 'Mancherial', 'Medak', 'Medchal–Malkajgiri', 'Mulugu', 'Nagarkurnool', 'Nalgonda', 'Narayanpet', 'Nirmal', 'Nizamabad', 'Peddapalli', 'Rajanna Sircilla', 'Rangareddy', 'Sangareddy', 'Siddipet', 'Suryapet', 'Vikarabad', 'Wanaparthy', 'Warangal Rural', 'Warangal Urban', 'Yadadri Bhuvanagiri'],
            'Uttar Pradesh': ['Agra', 'Aligarh', 'Allahabad', 'Ambedkar Nagar', 'Amethi', 'Amroha', 'Auraiya', 'Azamgarh', 'Baghpat', 'Bahraich', 'Ballia', 'Balrampur', 'Banda', 'Barabanki', 'Bareilly', 'Basti', 'Bijnor', 'Budaun', 'Bulandshahr', 'Chandauli', 'Chitrakoot', 'Deoria', 'Etah', 'Etawah', 'Faizabad', 'Farrukhabad', 'Fatehpur', 'Firozabad', 'Gautam Buddha Nagar', 'Ghaziabad', 'Ghazipur', 'Gonda', 'Gorakhpur', 'Hamirpur', 'Hapur', 'Hardoi', 'Hathras', 'Jalaun', 'Jaunpur', 'Jhansi', 'Kannauj', 'Kanpur Dehat', 'Kanpur Nagar', 'Kasganj', 'Kaushambi', 'Kheri', 'Kushinagar', 'Lalitpur', 'Lucknow', 'Maharajganj', 'Mahoba', 'Mainpuri', 'Mathura', 'Mau', 'Meerut', 'Mirzapur', 'Moradabad', 'Muzaffarnagar', 'Pilibhit', 'Pratapgarh', 'Raebareli', 'Rampur', 'Saharanpur', 'Sambhal', 'Sant Kabir Nagar', 'Shahjahanpur', 'Shamli', 'Shravasti', 'Siddharthnagar', 'Sitapur', 'Sonbhadra', 'Sultanpur', 'Unnao', 'Varanasi']
        };

        const cropsByStateAndSeason = {
            'Karnataka': {
                'Kharif': ['Arhar/Tur', 'Cotton(lint)', 'Dry chillies', 'Gram', 'Groundnut', 'Jowar', 'Maize', 'Moong(Green Gram)', 'Ragi', 'Rice', 'Sesamum', 'Small millets', 'Sugarcane', 'Sunflower', 'Urad'],
                'Rabi': ['Gram', 'Jowar', 'Maize', 'Rice', 'Wheat'],
                'Summer': ['Groundnut', 'Maize', 'Rice', 'Sunflower', 'Vegetables', 'Watermelon'],
                'Whole Year': ['Sugarcane', 'Banana', 'Coconut', 'Sweet potato', 'Tapioca']
            },
            'Andhra Pradesh': {
                'Kharif': ['Rice', 'Cotton(lint)', 'Groundnut', 'Maize', 'Sugarcane', 'Chillies', 'Turmeric'],
                'Rabi': ['Rice', 'Groundnut', 'Maize', 'Chillies', 'Bengal gram'],
                'Summer': ['Rice', 'Groundnut', 'Vegetables', 'Watermelon'],
                'Whole Year': ['Sugarcane', 'Banana', 'Coconut', 'Mango', 'Sweet orange']
            },
            'Gujarat': {
                'Kharif': ['Cotton(lint)', 'Groundnut', 'Rice', 'Bajra', 'Maize', 'Sesamum', 'Sugarcane'],
                'Rabi': ['Wheat', 'Jowar', 'Gram', 'Mustard'],
                'Summer': ['Groundnut', 'Bajra', 'Vegetables'],
                'Whole Year': ['Sugarcane', 'Banana', 'Mango', 'Sapota']
            },
            'Haryana': {
                'Kharif': ['Rice', 'Cotton(lint)', 'Bajra', 'Maize', 'Sugarcane'],
                'Rabi': ['Wheat', 'Rapeseed &Mustard', 'Gram', 'Barley'],
                'Summer': ['Vegetables', 'Cucurbits', 'Fodder'],
                'Whole Year': ['Sugarcane', 'Potato', 'Onion']
            },
            'Madhya Pradesh': {
                'Kharif': ['Soyabean', 'Maize', 'Rice', 'Cotton(lint)', 'Jowar', 'Arhar/Tur'],
                'Rabi': ['Wheat', 'Gram', 'Mustard', 'Masoor'],
                'Summer': ['Vegetables', 'Moong', 'Fodder'],
                'Whole Year': ['Sugarcane', 'Potato', 'Onion', 'Garlic']
            },
            'Maharashtra': {
                'Kharif': ['Cotton(lint)', 'Rice', 'Soyabean', 'Bajra', 'Maize', 'Sugarcane'],
                'Rabi': ['Jowar', 'Gram', 'Wheat', 'Sunflower'],
                'Summer': ['Groundnut', 'Vegetables', 'Fodder'],
                'Whole Year': ['Sugarcane', 'Banana', 'Grapes', 'Mango', 'Orange']
            },
            'Punjab': {
                'Kharif': ['Rice', 'Cotton(lint)', 'Maize', 'Sugarcane'],
                'Rabi': ['Wheat', 'Potato', 'Rapeseed &Mustard'],
                'Summer': ['Vegetables', 'Cucurbits', 'Fodder'],
                'Whole Year': ['Sugarcane', 'Potato', 'Onion']
            },
            'Tamil Nadu': {
                'Kharif': ['Rice', 'Groundnut', 'Sugarcane', 'Cotton(lint)', 'Maize'],
                'Rabi': ['Rice', 'Groundnut', 'Maize', 'Bengal gram'],
                'Summer': ['Rice', 'Groundnut', 'Vegetables', 'Watermelon'],
                'Whole Year': ['Sugarcane', 'Banana', 'Coconut', 'Mango', 'Turmeric']
            },
            'Telangana': {
                'Kharif': ['Cotton(lint)', 'Rice', 'Maize', 'Groundnut', 'Sugarcane'],
                'Rabi': ['Rice', 'Maize', 'Groundnut', 'Bengal gram'],
                'Summer': ['Rice', 'Vegetables', 'Watermelon'],
                'Whole Year': ['Sugarcane', 'Mango', 'Sweet orange', 'Banana']
            },
            'Uttar Pradesh': {
                'Kharif': ['Rice', 'Bajra', 'Maize', 'Arhar/Tur', 'Sugarcane'],
                'Rabi': ['Wheat', 'Potato', 'Rapeseed &Mustard', 'Gram'],
                'Summer': ['Vegetables', 'Cucurbits', 'Fodder'],
                'Whole Year': ['Sugarcane', 'Potato', 'Onion']
            }
        };

        function updateDistricts() {
            const stateSelect = document.getElementById('state');
            const districtSelect = document.getElementById('district');
            const selectedState = stateSelect.value;

            // Clear existing options
            districtSelect.innerHTML = '<option value="">Select a district</option>';
            
            // Add new options based on selected state
            if (selectedState && districtsByState[selectedState]) {
                districtsByState[selectedState].forEach(district => {
                    const option = document.createElement('option');
                    option.value = district;
                    option.textContent = district.replace(/_/g, ' ');
                    districtSelect.appendChild(option);
                });
            }
            
            // Reset crop dropdown when state changes
            updateCrops();
        }

        function updateCrops() {
            const stateSelect = document.getElementById('state');
            const seasonSelect = document.getElementById('season');
            const cropSelect = document.getElementById('crop');
            const selectedState = stateSelect.value;
            const selectedSeason = seasonSelect.value;

            // Clear existing options
            cropSelect.innerHTML = '<option value="">Select a crop</option>';

            // Add new options based on selected state and season
            if (selectedState && selectedSeason && cropsByStateAndSeason[selectedState] && cropsByStateAndSeason[selectedState][selectedSeason]) {
                cropsByStateAndSeason[selectedState][selectedSeason].forEach(crop => {
                    const option = document.createElement('option');
                    option.value = crop;
                    option.textContent = crop;
                    cropSelect.appendChild(option);
                });
            }
        }

        // Initialize districts on page load
        document.addEventListener('DOMContentLoaded', () => {
            updateDistricts();
            // Add event listener for season changes
            document.getElementById('season').addEventListener('change', updateCrops);
        });
    </script>
</body>
</html>
