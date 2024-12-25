<?php 
session_start();
ini_set('memory_limit', '-1');
$userlogin=$_SESSION['farmer_login_user'];
// Establishing Connection with Server by passing server_name, user_id and password as a parameter
require('../sql.php'); // Includes Login Script

if(isset($_POST['Crop_submit'])){
    $x=0.0;
    $y=0;
    
    // Check if it's a custom crop
    $trade_crop = ($_POST['crops'] == 'other') ? 
        mysqli_real_escape_string($conn, $_POST['custom_crop']) : 
        $_POST['crops'];
    
    $quantity=$_POST['trade_farmer_cropquantity'];
    $costperkg=$_POST['trade_farmer_cost'];
    
    $query1="SELECT farmer_id from farmerlogin where email='".$userlogin."';";
    $run = mysqli_query($conn,$query1);
    $row=mysqli_fetch_array($run);
    $farmer_pid= $row[0];
    
    $query2="INSERT INTO `farmer_crops_trade`(`farmer_fkid`, `Trade_crop`, `Crop_quantity`,`costperkg`) 
    VALUES ($farmer_pid,'$trade_crop', $quantity, $costperkg);";
    $result = mysqli_query($conn, $query2);


    $query="SELECT costperkg from farmer_crops_trade where Trade_crop='$trade_crop'";
    $result = mysqli_query($conn, $query);
    while($row = $result->fetch_assoc()) {
        $x=$x+$row["costperkg"];
        $y++;
    }

    $x=CEIL($x/$y);
    $x=$x+CEIL($x*0.5);

    $query3="UPDATE farmer_crops_trade SET msp='$x' where Trade_crop='$trade_crop'";
    $result = mysqli_query($conn, $query3);

    // First check if the crop exists in production_approx
    $check_crop = "SELECT COUNT(*) as count FROM production_approx WHERE crop='$trade_crop'";
    $check_result = mysqli_query($conn, $check_crop);
    $row = mysqli_fetch_assoc($check_result);
    
    if ($row['count'] > 0) {
        // If crop exists, update quantity
        $query4 = "UPDATE production_approx SET quantity=quantity+'$quantity' WHERE crop='$trade_crop'";
    } else {
        // If crop doesn't exist, insert new record
        $query4 = "INSERT INTO production_approx (crop, quantity) VALUES ('$trade_crop', '$quantity')";
    }
    $result = mysqli_query($conn, $query4);

    if (!$result) {
        echo "<script type='text/javascript'>alert('Error updating crop stock: " . mysqli_error($conn) . "');
        window.location='ftradecrops.php';</script>";
    } else {
        echo "<script type='text/javascript'>alert('Crop Details Added Successfully');
        window.location='ftradecrops.php';</script>";
    }

}

?>
