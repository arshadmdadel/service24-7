<?php
session_start(); // Start the session to access session variables

// Database connection settings
$servername = "localhost";
$username = "root"; // Update with your database username
$password = ""; // Update with your database password
$dbname = "service24/7"; // Update with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the order ID from the session
$order_id = $_SESSION['orderID'] ?? null;
$_SESSION['math']=$order_id+1;

// Get the rating and comment from the POST request
$rating = $_POST['rating'] ?? null;
$comment = $_POST['comment'] ?? null;

// If the comment is empty, set it to "none"
$comment = empty($comment) ? "none" : $comment;

if (!$rating || !$order_id) {
    // Show a pop-up if rating or order_id is missing
    echo "<script>
        alert('Please provide a rating and ensure a valid order ID is available.');
        window.history.back(); // Redirect back to the review form
    </script>";
    exit;
}

// Step 1: Insert the review into the 'rating' table
$sql_insert = "INSERT INTO rating (order_id, rating, comment) VALUES (?, ?, ?)";
if ($stmt = $conn->prepare($sql_insert)) {
    $stmt->bind_param("iis", $order_id, $rating, $comment);
    if ($stmt->execute()) {
        echo "Review submitted successfully!<br>";
    } else {
        echo "Error inserting review: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Step 2: Find the worker_id from the 'order' table using the order_id
$sql_worker = "SELECT worker_id FROM `order` WHERE id = ?";
if ($stmt = $conn->prepare($sql_worker)) {
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->bind_result($worker_id);
    if ($stmt->fetch()) {
        echo "Worker ID found: $worker_id<br>";
    } else {
        echo "No worker found for the given order ID.<br>";
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
}

// Step 3: Calculate the new average star rating for the worker
$sql_avg = "SELECT AVG(rating) AS avg_rating, COUNT(rating.id) AS total_ratings FROM rating 
            INNER JOIN `order` ON rating.order_id = `order`.id 
            WHERE worker_id = ?";
if ($stmt = $conn->prepare($sql_avg)) {
    $stmt->bind_param("i", $worker_id);
    $stmt->execute();
    $stmt->bind_result($avg_rating, $total_ratings);
    if ($stmt->fetch()) {
        echo "Raw average rating: $avg_rating<br>";
        echo "Total number of ratings: $total_ratings<br>";
        // Round the average rating according to the given ranges
        if ($avg_rating >= floor($avg_rating) + 0.68) {
            $avg_rating = ceil($avg_rating); // Round up to the next integer
        } elseif ($avg_rating >= floor($avg_rating) + 0.34) {
            $avg_rating = floor($avg_rating) + 0.5; // Round to x.5
        } else {
            $avg_rating = floor($avg_rating); // Round down to the integer
        }
        echo "Rounded average rating: $avg_rating<br>";
    } else {
        echo "Error calculating average rating.<br>";
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();
}

// Step 4: Update the worker's rating and number of ratings in the 'worker' table
$sql_update = "UPDATE worker SET rating = ?, num_of_rating = ? WHERE id = ?";
if ($stmt = $conn->prepare($sql_update)) {
    $stmt->bind_param("dii", $avg_rating, $total_ratings, $worker_id);
    if ($stmt->execute()) {
        echo "Worker's rating and number of ratings updated successfully!<br>";
    } else {
        echo "Error updating worker's rating and number of ratings: " . $stmt->error . "<br>";
    }
    $stmt->close();
} else {
    echo "Error preparing update statement: " . $conn->error . "<br>";
}


$conn->close();
?>
