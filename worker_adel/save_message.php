<?php


session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user'])) {
    header("Location: user_login.php");
    exit();
}

$workerID = $_SESSION['worker_id'];
// Database configuration
$host = "localhost";        // Replace with your database host
$username = "root";         // Replace with your database username
$password = "";             // Replace with your database password
$dbname = "service24/7";  // Replace with your database name

// Set headers
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

// Create a connection to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error,
    ]);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize POST data
    $content = isset($_POST['message']) ? $conn->real_escape_string($_POST['message']) : null;
    $user_id = isset($_POST['sender']) ? $conn->real_escape_string($_POST['sender']) : null; // Assuming "sender" maps to user_id
    $flag = isset($_POST['flag']) ? $conn->real_escape_string($_POST['flag']) : null; // Assuming "sender" maps to user_id
    $order_id = isset($_POST['order_id']) ? $conn->real_escape_string($_POST['order_id']) : null;

    // Validate required fields
    if ($content && $user_id) {
        // Prepare and execute the SQL query to insert data
        $sql = "INSERT INTO messages (user_id, worker_id, content, flag, order_id) VALUES ('$user_id', $workerID, '$content', $flag, $order_id)";

        if ($conn->query($sql) === TRUE) {

            echo json_encode([
                "status" => "success",
                "message" => "Message saved successfully.",
                "data" => [
                    "id" => $conn->insert_id, // Get the ID of the inserted row
                    "user_id" => $user_id,
                    "worker_id" => $workerID,
                    "content" => $content,
                    "flag" => $flag,
                    "order_id" => $order_id,
                ]
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Error saving message: " . $conn->error,
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required fields."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method. Use POST."
    ]);
}

// Close the database connection
// header("Location: worker_homepage1.php");


// header("Refresh:0; url=worker_homepage1.php");


$conn->close();
?>
