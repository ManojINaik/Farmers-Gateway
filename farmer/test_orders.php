<?php
include ('fsession.php');
require_once("../sql.php");

if(!isset($_SESSION['farmer_login_user'])){
    header("location: ../index.php");
    exit();
} 

// Get farmer ID from session
$user_check = $_SESSION['farmer_login_user'];
$query = "SELECT farmer_id FROM farmerlogin WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_check);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$farmer_id = $row['farmer_id'];

echo "<h2>Debug Information</h2>";
echo "<pre>";
echo "Farmer Email: " . htmlspecialchars($user_check) . "\n";
echo "Farmer ID: " . htmlspecialchars($farmer_id) . "\n";
echo "</pre>";

// Check order_history table
echo "<h3>Orders in order_history table</h3>";
$query1 = "SELECT * FROM order_history WHERE farmer_id = ?";
$stmt1 = $conn->prepare($query1);
$stmt1->bind_param("i", $farmer_id);
$stmt1->execute();
$result1 = $stmt1->get_result();

echo "<pre>";
echo "Number of orders found: " . $result1->num_rows . "\n\n";
while ($row = $result1->fetch_assoc()) {
    print_r($row);
    echo "\n";
}
echo "</pre>";

// Check orders table
echo "<h3>Related orders in orders table</h3>";
$query2 = "SELECT o.* FROM orders o 
           JOIN order_history oh ON o.order_id = oh.order_id 
           WHERE oh.farmer_id = ?";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param("i", $farmer_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

echo "<pre>";
echo "Number of orders found: " . $result2->num_rows . "\n\n";
while ($row = $result2->fetch_assoc()) {
    print_r($row);
    echo "\n";
}
echo "</pre>";

// Check the combined data
echo "<h3>Combined Order Data</h3>";
$query3 = "SELECT oh.order_id, oh.crop_name, oh.quantity, oh.price, oh.total_price,
                  o.order_date, o.status, o.customer_id,
                  cl.cust_name, cl.phone_no, cl.address
           FROM order_history oh
           LEFT JOIN orders o ON o.order_id = oh.order_id
           LEFT JOIN custlogin cl ON o.customer_id = cl.cust_id
           WHERE oh.farmer_id = ?";
$stmt3 = $conn->prepare($query3);
$stmt3->bind_param("i", $farmer_id);
$stmt3->execute();
$result3 = $stmt3->get_result();

echo "<pre>";
echo "Number of combined records: " . $result3->num_rows . "\n\n";
while ($row = $result3->fetch_assoc()) {
    print_r($row);
    echo "\n";
}
echo "</pre>";
?>
