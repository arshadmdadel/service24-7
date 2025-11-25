<?php
session_start(); // Assuming session contains the logged-in user's ID

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'service24/7');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = array('success' => false);

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id']; 

// Update the state for all orders where the user ID matches and the current state is 1
$query = "UPDATE `order` SET `state` = 0 WHERE `state` = 1 AND `user_id` = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id); // Bind the user ID parameter to the query

if ($stmt->execute()) {
    // Check if rows were updated
    if ($stmt->affected_rows > 0) {
        $response['success'] = true;
    } else {
        $response['message'] = "No records updated.";
    }
} else {
    $response['message'] = "Failed to update state.";
}

$stmt->close(); // Close the prepared statement
$conn->close(); // Close the database connection

header('Content-Type: application/json');
echo json_encode($response);
?>
