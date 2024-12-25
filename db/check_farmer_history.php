<?php
require_once("../sql.php");

// Check if table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'farmer_history'");
if(mysqli_num_rows($table_check) > 0) {
    echo "Table 'farmer_history' exists.\n";
    
    // Get table structure
    $result = mysqli_query($conn, "DESCRIBE farmer_history");
    echo "\nTable structure:\n";
    while($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    
    // Check date values
    $date_check = mysqli_query($conn, "SELECT DISTINCT date FROM farmer_history WHERE date IS NOT NULL ORDER BY date");
    echo "\nUnique date values:\n";
    while($row = mysqli_fetch_assoc($date_check)) {
        echo $row['date'] . "\n";
    }
    
    // Fix date column if needed
    $alter_query = "ALTER TABLE farmer_history MODIFY COLUMN date VARCHAR(20)";
    if(mysqli_query($conn, $alter_query)) {
        echo "\nDate column modified successfully\n";
    } else {
        echo "\nError modifying date column: " . mysqli_error($conn) . "\n";
    }
    
    // Update any invalid dates
    $update_query = "UPDATE farmer_history SET date = DATE_FORMAT(NOW(), '%d/%m/%Y') WHERE date = '0000-00-00 00:00:00' OR date IS NULL OR date = ''";
    if(mysqli_query($conn, $update_query)) {
        echo "\nInvalid dates updated successfully\n";
    } else {
        echo "\nError updating invalid dates: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "Table 'farmer_history' does not exist.";
}
?>
