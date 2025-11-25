<?php
// Access the worker ID from the URL
if (isset($_GET['worker_id'])) {
    $worker_id = intval($_GET['worker_id']); // Sanitize the input
    echo "<h1>Order Page</h1>";
    echo "<p>Selected Worker ID: " . $worker_id . "</p>";

    // Perform any logic you need, such as retrieving worker details from the database
    $host = 'localhost'; // Change to your DB host
    $username = 'root';  // Change to your DB username
    $password = '';      // Change to your DB password
    $database = 'service24/7'; // Change to your DB name

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve worker details
    $sql = "SELECT * FROM worker WHERE id = $worker_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $worker = $result->fetch_assoc();
        echo "<h2>Worker Details:</h2>";
        echo "<p>Name: " . $worker['fullname'] . "</p>";
        echo "<p>Rating: " . $worker['rating'] . "</p>";
        echo "<p>Details: " . $worker['detail'] . "</p>";
    } else {
        echo "<p>Worker not found.</p>";
    }

    $conn->close();
} else {
    echo "<p>No worker selected. Please go back and select a worker.</p>";
}
?>
