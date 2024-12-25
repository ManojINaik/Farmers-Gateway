<?php 
session_start(); 
require('../sql.php'); // Includes SQL connection script

// Check if the crop is 'other' and use the custom crop name
$crops = isset($_POST['custom_crop']) && $_POST['crop'] == 'other' 
    ? mysqli_real_escape_string($conn, $_POST['custom_crop']) 
    : $_POST['crop'];

$x = 0.0;    
$y = 0;

$query = "SELECT costperkg FROM farmer_crops_trade WHERE Trade_crop='$crops'";
$result = mysqli_query($conn, $query);

while($row = $result->fetch_assoc()) {
    $x = $x + $row["costperkg"];
    $y++;
}

$response = [];
if ($y != 0) {
    $x = CEIL($x / $y);
    $response = [
        'status' => 'success',
        'crop' => $crops,
        'price' => $x
    ];
} else {
    $response = [
        'status' => 'error',
        'message' => 'No price data available'
    ];
}

echo json_encode($response);
?>
