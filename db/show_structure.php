<?php
require_once("../sql.php");

// Get table structure
$result = mysqli_query($conn, "DESCRIBE farmerlogin");
if ($result) {
    echo "Table structure for farmerlogin:\n";
    while($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error getting table structure: " . mysqli_error($conn);
}
?>
