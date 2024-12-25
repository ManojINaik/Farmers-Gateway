<?php
require_once("../sql.php");

// Check if table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'farmerlogin'");
if(mysqli_num_rows($table_check) > 0) {
    echo "Table 'farmerlogin' exists.\n";
    
    // Get table structure
    $result = mysqli_query($conn, "DESCRIBE farmerlogin");
    echo "\nTable structure:\n";
    while($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Table 'farmerlogin' does not exist.";
}
?>
