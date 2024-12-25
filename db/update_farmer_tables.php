<?php
require_once('../includes/db.php');

try {
    // Read and execute the SQL file
    $sql = file_get_contents('create_farmer_tables.sql');
    
    if ($connection->multi_query($sql)) {
        do {
            // Consume all results
            if ($result = $connection->store_result()) {
                $result->free();
            }
        } while ($connection->next_result());
        
        echo "Farmer tables created successfully!\n";
    }
} catch (Exception $e) {
    echo "Error creating farmer tables: " . $e->getMessage() . "\n";
}

$connection->close();
?>
