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

<?php
// ... [Keep existing PHP code for API calls] ...
$display_district_name ="";

$display_district="Select F_District from farmerlogin WHERE email='$user_check'";
$display_district_result=mysqli_query($conn,$display_district);
$display_district_name = mysqli_fetch_array($display_district_result);
$District_name_farmer=$display_district_name[0];

ini_set('memory_limit', '-1');
$url = 'static/citylist.json';
$data = file_get_contents($url);
$district= json_decode($data);

$district_weather_id=0;

foreach ($district as $district) {
    if ($district->name == trim($District_name_farmer)) {
        $district_weather_id=$district->id;
    }
}
if($district_weather_id<=0){
    $district_weather_id=1253952;
}
$city_weather_id=strval($district_weather_id);

date_default_timezone_set("Asia/Kolkata");
$apiKey = "870887df4d2b01335921fe396c69a360";
$cityId = $city_weather_id;

$googleApiUrl ="https://api.openweathermap.org/data/2.5/forecast?id=" . $cityId . "&lang=en&units=metric&APPID=" . $apiKey;
$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);

curl_close($ch);
$data = json_decode($response);
$currentTime = time();
$forecast = $data->list;
?>

<!DOCTYPE html>
<html>
<?php include ('fheader.php');  ?>

<head>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .weather-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .weather-card:hover {
            transform: translateY(-5px);
        }
        .current-weather {
            background: linear-gradient(120deg, #2ecc71, #27ae60);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .weather-icon-large {
            width: 100px;
            height: 100px;
        }
        .weather-icon-small {
            width: 40px;
            height: 40px;
        }
        .forecast-item {
            border-left: 4px solid #2ecc71;
            margin-bottom: 10px;
            padding: 15px;
            background: white;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .forecast-item:hover {
            background: #f8f9fa;
            border-left-color: #27ae60;
        }
        .weather-stat {
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            background: #f8f9fa;
            margin-bottom: 15px;
        }
        .stat-icon {
            font-size: 24px;
            margin-bottom: 10px;
            color: #2ecc71;
        }
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>

    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</head>

<body class="bg-white" id="top">
    <?php include ('fnav.php');  ?>
 	
    <section class="section section-shaped section-lg">
        <div class="shape shape-style-1 shape-primary">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="container py-5">
            <div class="row">
                <div class="col-12 text-center mb-4">
                    <h2 class="display-3 text-white">Weather Forecast</h2>
                    <p class="text-white">5-Day Weather Forecast for <?php echo $District_name_farmer; ?></p>
                </div>
            </div>

            <!-- Current Weather -->
            <div class="current-weather mb-5">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="mb-3">Current Weather</h3>
                        <div class="d-flex align-items-center">
                            <img src="http://openweathermap.org/img/w/<?php echo $forecast[0]->weather[0]->icon; ?>.png" class="weather-icon-large mr-4">
                            <div>
                                <h2 class="display-4 mb-0"><?php echo round($forecast[0]->main->temp); ?>°C</h2>
                                <p class="lead mb-0"><?php echo $forecast[0]->weather[0]->main; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-6">
                                <div class="weather-stat">
                                    <i class="fas fa-wind stat-icon"></i>
                                    <h4><?php echo $forecast[0]->wind->speed; ?> km/h</h4>
                                    <p class="mb-0">Wind Speed</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="weather-stat">
                                    <i class="fas fa-tint stat-icon"></i>
                                    <h4><?php echo $forecast[0]->main->humidity; ?>%</h4>
                                    <p class="mb-0">Humidity</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5-Day Forecast -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-lg">
                        <div class="card-body">
                            <h4 class="card-title mb-4">5-Day Forecast</h4>
                            <div class="table-responsive">
                                <table class="table table-hover" id="forecastTable">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Temperature</th>
                                            <th>Weather</th>
                                            <th>Humidity</th>
                                            <th>Wind</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($forecast as $f): 
                                            $date = date('M d, Y', strtotime($f->dt_txt));
                                            $time = date('H:i', strtotime($f->dt_txt));
                                        ?>
                                        <tr class="forecast-item">
                                            <td>
                                                <strong><?php echo $date; ?></strong><br>
                                                <small><?php echo $time; ?></small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="http://openweathermap.org/img/w/<?php echo $f->weather[0]->icon; ?>.png" class="weather-icon-small mr-2">
                                                    <span><?php echo round($f->main->temp); ?>°C</span>
                                                </div>
                                            </td>
                                            <td><?php echo $f->weather[0]->description; ?></td>
                                            <td>
                                                <i class="fas fa-tint text-primary"></i>
                                                <?php echo $f->main->humidity; ?>%
                                            </td>
                                            <td>
                                                <i class="fas fa-wind text-info"></i>
                                                <?php echo $f->wind->speed; ?> km/h
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weather Charts -->
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="chart-container">
                        <canvas id="temperatureChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-container">
                        <canvas id="humidityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable
            if ($.fn.DataTable && $('#forecastTable').length) {
                $('#forecastTable').DataTable({
                    pageLength: 10,
                    order: [[0, 'asc']],
                    responsive: true
                });
            }

            // Prepare data for charts
            const forecastData = <?php 
                if (!empty($forecast)) {
                    echo json_encode(array_map(function($f) { 
                        return [
                            'temp' => round($f->main->temp, 1),
                            'humidity' => $f->main->humidity,
                            'time' => date('H:i', strtotime($f->dt_txt))
                        ];
                    }, array_slice($forecast, 0, 8)));
                } else {
                    echo '[]';
                }
            ?>;

            // Only create charts if we have data
            if (forecastData.length > 0) {
                const labels = forecastData.map(item => item.time);
                const temperatures = forecastData.map(item => item.temp);
                const humidity = forecastData.map(item => item.humidity);

                // Temperature Chart
                if (document.getElementById('temperatureChart')) {
                    new Chart(document.getElementById('temperatureChart'), {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Temperature (°C)',
                                data: temperatures,
                                borderColor: '#2ecc71',
                                tension: 0.4,
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Temperature Trend'
                                }
                            }
                        }
                    });
                }

                // Humidity Chart
                if (document.getElementById('humidityChart')) {
                    new Chart(document.getElementById('humidityChart'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Humidity (%)',
                                data: humidity,
                                backgroundColor: '#3498db',
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Humidity Levels'
                                }
                            }
                        }
                    });
                }
            } else {
                console.error('No forecast data available for charts');
            }
        });
    </script>
    <?php include("../modern-footer.php"); ?>
</body>
</html>
