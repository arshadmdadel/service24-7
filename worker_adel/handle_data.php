<?php
session_start();
$workerID = $_SESSION['worker_id'];


// Database connection
$conn = new mysqli('localhost', 'root', '', 'service24/7');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle accept/decline actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderID = $_POST['orderID'] ?? '';
    $action = $_POST['action'] ?? '';


    if (!empty($orderID)) {
        if ($action === 'accept' || $action === 'decline') {
            $acceptStatus = ($action === 'accept') ? 1 : 0;
            $stmt = $conn->prepare("UPDATE `order` SET accept_status = ?, notification_status = 1 , state = 1 WHERE id = ? AND worker_id = ?");
            $stmt->bind_param("isi", $acceptStatus, $orderID, $workerID);
            $stmt->execute();
            $stmt->close();
        } elseif ($action === 'done') {
            $stmt = $conn->prepare("UPDATE `order` SET accept_status = 2 , state = 1 WHERE id = ? AND worker_id = ?");
            $stmt->bind_param("is", $orderID, $workerID);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: worker_homepage.php");
    }
}
hea
?>