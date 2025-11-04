<?php
/**
 * Quick script to add down_payment_amount column to bookings table
 * Run this once: php add_down_payment_field.php
 */

require __DIR__ . '/vendor/autoload.php';

$config = config('Database');
$db = \Config\Database::connect();

echo "Checking if down_payment_amount column exists...\n";

try {
    // Check if column exists
    $query = $db->query("SHOW COLUMNS FROM bookings LIKE 'down_payment_amount'");
    $exists = $query->getNumRows() > 0;
    
    if (!$exists) {
        echo "Adding down_payment_amount column...\n";
        
        // Add the column
        $db->query("ALTER TABLE bookings ADD COLUMN down_payment_amount DECIMAL(10,2) NULL AFTER total_price");
        
        echo "Column added successfully!\n";
        
        // Update existing bookings that have payment references but no down payment amount
        echo "Updating existing bookings...\n";
        
        $updateQuery = "UPDATE bookings 
                       SET down_payment_amount = total_price * 0.5 
                       WHERE payment_status = 'pending' 
                         AND payment_method IS NOT NULL 
                         AND payment_reference IS NOT NULL 
                         AND payment_reference != ''";
        
        $result = $db->query($updateQuery);
        $affected = $db->affectedRows();
        
        echo "Updated $affected existing bookings with down payment amounts.\n";
    } else {
        echo "Column already exists!\n";
    }
    
    echo "Done!\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

