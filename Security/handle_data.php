<?php
session_start(); // Start the session to store data

// Check if the form has been submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the form submission
    $worker_id = $_POST['worker_id'] ?? null;
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $pickup_location = $_POST['pickup_location'] ?? null;

    // Check if all necessary data has been provided
    if ($worker_id && $start_date && $end_date && $pickup_location) {
        // Assign values to session
        $_SESSION['worker_id'] = $worker_id;
        $_SESSION['start_date'] = $start_date;
        $_SESSION['end_date'] = $end_date;
        $_SESSION['pickup_location'] = $pickup_location;

        // Optionally, you could also assign more data related to the worker here:
        $_SESSION['worker_fullname'] = $worker_fullname ?? ''; // If you have this available
        $_SESSION['worker_rating'] = $worker_rating ?? '';    // If you have this available
        $_SESSION['worker_price'] = $worker_price ?? '';      // If you have this available

        // Redirect to the next page or success page
        header("Location: Petorder.php"); // Or wherever you want to redirect
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