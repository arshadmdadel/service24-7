<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $conn = new mysqli('localhost', 'root', '', 'your_database');
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    // Get worker data from pending table
    $result = $conn->query("SELECT * FROM worker_pending WHERE id = $id");
    $worker = $result->fetch_assoc();

    // Move worker to the main table
    $stmt = $conn->prepare("INSERT INTO worker (nid, fullname, phone, email, password, work_type, detail, price, picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssss", $worker['nid'], $worker['fullname'], $worker['phone'], $worker['email'], $worker['password'], $worker['work_type'], $worker['detail'], $worker['price'], $worker['picture']);
    $stmt->execute();

    // Remove worker from pending table
    $conn->query("DELETE FROM worker_pending WHERE id = $id");

    echo "Worker approved successfully.";
    $conn->close();

    header("Location: index.php");
    exit();
}
?>
