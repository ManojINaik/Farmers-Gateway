<?php
session_start(); // Starting Session
$error = ''; // Variable To Store Error Message

require('../sql.php'); // Includes Login Script

if(isset($_POST ['farmerlogin'])) {
  $farmer_email=$_POST['farmer_email'];
  $farmer_password=$_POST['farmer_password'];
  //$farmer_password=SHA1($farmer_password);


  $farmerquery = "SELECT * from `farmerlogin` where email='".$farmer_email."' and password='".$farmer_password."' ";
  $result = mysqli_query($conn, $farmerquery);
  $rowcount=mysqli_num_rows($result);
  if ($rowcount==true) {
    $_SESSION['farmer_login_user']=$farmer_email; // Initializing Session
    $_SESSION['IS_LOGIN']=$farmer_email; // Set login status directly


    header("location: fprofile.php"); // Redirect directly to profile
    } 
    else  {
       $error = "Username or Password is invalid";
     }
    
 mysqli_close($conn); // Closing Connection

}

?>
