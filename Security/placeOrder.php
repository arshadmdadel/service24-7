<?php
session_start();

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'service24/7';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve data from session
$user_id = $_SESSION['user_id'] ?? null;
$worker_id = intval($_SESSION['worker_id'] ?? 0);
$start_date = $_SESSION['start_date'] ?? null;
$end_date = $_SESSION['end_date'] ?? null;
$pickup_location = $_SESSION['pickup_location'] ?? null;
$pet_type = $_SESSION['pet_type'] ?? null;
$place_price = floatval($_SESSION['price'] ?? 0);
$worker_price = floatval($_SESSION['worker_price'] ?? 0);

// Validate session data
if (!$user_id || !$worker_id || !$start_date || !$end_date || !$pickup_location || !$pet_type) {
    die("Missing required session data.");
}

// Calculate the total cost and number of days
$start = new DateTime($start_date);
$end = new DateTime($end_date);
$days = $start->diff($end)->days;

if ($days <= 0) {
    die("Invalid date range.");
}

$total_cost = ($worker_price + $place_price) * $days;

// Insert data into the `order` table
$order_sql = "
    INSERT INTO `order` (service_name, user_id, worker_id)
    VALUES ('0', '$user_id', '$worker_id')
";

if ($conn->query($order_sql) === TRUE) {
    $order_id = $conn->insert_id; // Get the ID of the newly inserted order

    // Insert data into the `pet_caring` table
    $pet_caring_sql = "
        INSERT INTO pet_caring (pet_type, place_price, start_date, end_date, pickup_location, total_cost, order_id)
        VALUES ('$pet_type', '$place_price', '$start_date', '$end_date', '$pickup_location', '$total_cost', '$order_id')
    ";

    if ($conn->query($pet_caring_sql) === TRUE) {
        // Check if the worker already has availability in the given date range
        $availability_sql = "
            SELECT num_of_work FROM worker_availability 
            WHERE worker_id = $worker_id 
            AND start_date <= '$end_date' AND end_date >= '$start_date'
        ";

        $result = $conn->query($availability_sql);

        if ($result && $result->num_rows > 0) {
            // Update `num_of_work` if record exists
            $worker = $result->fetch_assoc();
            $new_num_of_work = $worker['num_of_work'] + 1;

            $update_availability_sql = "
                UPDATE worker_availability 
                SET num_of_work = $new_num_of_work 
                WHERE worker_id = $worker_id 
                AND start_date <= '$end_date' AND end_date >= '$start_date'
            ";

            if ($conn->query($update_availability_sql) === TRUE) {
                echo "Order placed successfully and worker availability updated!";
                // header("Location: success.php"); // Uncomment to redirect
                exit();
            } else {
                echo "Error updating worker availability: " . $conn->error;
            }
        } else {
            // Insert new record if no availability exists for the date range
            $insert_availability_sql = "
                INSERT INTO worker_availability (worker_id, start_date, end_date, num_of_work)
                VALUES ('$worker_id', '$start_date', '$end_date', 1)
            ";

            if ($conn->query($insert_availability_sql) === TRUE) {
                echo "Order placed successfully and worker availability added!";
                header("Location: ../home_page.html"); // Uncomment to redirect
                exit();
            } else {
                echo "Error adding worker availability: " . $conn->error;
            }
        }
    } else {
        // Rollback `order` if `pet_caring` insertion fails
        $conn->query("DELETE FROM `order` WHERE id = '$order_id'");
        echo "Error in pet_caring: " . $conn->error;
    }
} else {
    echo "Error in order: " . $conn->error;
}

$conn->close();
?>
