<?php
require_once('../sql.php');

// Execute order tables SQL first
$order_tables_sql = file_get_contents('create_order_tables.sql');
try {
    if ($conn->multi_query($order_tables_sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
        echo "Success: Order tables created/updated<br>";
    } else {
        echo "Error creating order tables: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

// Execute farmer tables SQL
$farmer_tables_sql = file_get_contents('create_farmer_tables.sql');
try {
    if ($conn->multi_query($farmer_tables_sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
        echo "Success: Farmer tables created/updated<br>";
    } else {
        echo "Error creating farmer tables: " . $conn->error . "<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

// SQL commands to update the database
$sql_commands = [
    // Add price_per_kg column if it doesn't exist
    "ALTER TABLE production_approx ADD COLUMN IF NOT EXISTS price_per_kg decimal(10,2) NOT NULL DEFAULT '0.00'",
    
    // Update prices for existing crops
    "UPDATE production_approx SET price_per_kg = CASE crop
        WHEN 'arhar' THEN 85.00
        WHEN 'bajra' THEN 45.00
        WHEN 'barley' THEN 35.00
        WHEN 'cotton' THEN 75.00
        WHEN 'gram' THEN 65.00
        WHEN 'jowar' THEN 40.00
        WHEN 'jute' THEN 55.00
        WHEN 'lentil' THEN 95.00
        WHEN 'maize' THEN 50.00
        WHEN 'moong' THEN 80.00
        WHEN 'ragi' THEN 45.00
        WHEN 'rice' THEN 60.00
        WHEN 'soyabean' THEN 70.00
        WHEN 'urad' THEN 85.00
        WHEN 'wheat' THEN 55.00
        ELSE price_per_kg
    END"
];

// Execute each SQL command
foreach ($sql_commands as $sql) {
    try {
        if ($conn->query($sql)) {
            echo "Success: " . substr($sql, 0, 50) . "...<br>";
        } else {
            echo "Error: " . $conn->error . "<br>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }
}

echo "<br>Database update completed!";
?>
