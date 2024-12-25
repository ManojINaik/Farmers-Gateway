<?php
require_once("../sql.php");

$result = mysqli_query($conn, "SHOW COLUMNS FROM farmerlogin");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . "\n";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
