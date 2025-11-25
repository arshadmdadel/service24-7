<?php
// config.php: Database connection and session initialization
session_start();

$host = "localhost"; // Database host
$username = "root";  // Database username
$password = "";      // Database password
$dbname = "service24/7"; // Replace with your database name

try {
    $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $dbname, $username, $password);
    // Set PDO to throw exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If the connection fails, output the error
    die("Connection failed: " . $e->getMessage());
}

// You can also define other configurations here, such as for file storage or settings.
?>
