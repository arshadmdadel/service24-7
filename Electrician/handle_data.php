<?php
session_start(); // Start the session to store data

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'service24/7';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form has been submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the form submission
    $worker_id = $_POST['worker_id'] ?? null;
    $date = $_POST['date'] ?? null;
    $pickup_location = $_POST['pickup_location'] ?? null;

    // Check if all necessary data has been provided
    if ($worker_id && $date && $pickup_location) {
        // Assign values to session
        $_SESSION['worker_id'] = $worker_id;
        $_SESSION['start_date'] = $date;
        $_SESSION['end_date'] = $date; // Start and end dates are the same
        $_SESSION['pickup_location'] = $pickup_location;

        // Redirect to the next page or success page
        header("Location: order.php"); // Or wherever you want to redirect
        exit(); // Make sure the script stops here
    } else {
        // Handle the case when the data is incomplete
        echo "Please complete all the required fields.";
    }
} else {
    // Handle invalid request method (if necessary)
    echo "Invalid request method.";
}
?>
