<?php
// test_mail.php
// This file tests the database connection established by config.php.

// Include the config.php file.
// This will make the $pdo variable available in this script's scope
// if the connection in config.php was successful.
require_once __DIR__ . '/config.php'; // Use __DIR__ for a more robust path

// Now that config.php has been included, $pdo should be defined (if connection was successful).
// You can proceed to use $pdo for database operations.
try {
    // Attempt a simple query to verify the connection is active
    $stmt = $pdo->query("SELECT 1"); // A simple query that returns 1
    if ($stmt) {
        echo "Database connection successful in test_mail.php!";
    } else {
        echo "Database connection established, but query failed in test_mail.php.";
    }
} catch (PDOException $e) {
    // If any PDO operation fails here, catch the exception.
    echo "Database query failed: " . $e->getMessage();
}

// You can now close the connection if you are done with it, though PDO connections
// are often implicitly closed at the end of the script execution.
// $pdo = null;
?>