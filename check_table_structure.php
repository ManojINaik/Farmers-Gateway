<?php
require_once("includes/db.php");

$table_name = 'farmtube_likes';

// Check table structure
$query = "DESCRIBE $table_name";
$result = mysqli_query($connection, $query);

if ($result) {
    echo "Table Structure for $table_name:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        print_r($row);
    }
} else {
    echo "Error: " . mysqli_error($connection);
}

mysqli_close($connection);
?>
