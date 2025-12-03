<?php
require_once __DIR__ . '/config.php';

try {
    // Read the migration file
    $sql = file_get_contents(__DIR__ . '/migrations/rename_property_name_to_title.sql');
    
    // Execute the migration
    $pdo->exec($sql);
    
    echo "Migration completed successfully!\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
