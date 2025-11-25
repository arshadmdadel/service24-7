<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nid = $_POST['nid'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $work_type = $_POST['work_type'];
    $detail = $_POST['detail'];
    $price = $_POST['price'];

    // Handle file upload
    $target_dir = "uploads/";
    $picture_name = basename($_FILES["picture"]["name"]);
    $target_file = $target_dir . $picture_name;

    if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
        // Save data to worker_pending table
        $stmt = $conn->prepare("INSERT INTO worker(nid, fullname, phone, email, password, work_type, detail, price, picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssisss", $nid, $fullname, $phone, $email, $password, $work_type, $detail, $price, $picture_name);

        if ($stmt->execute()) {
            echo "Registration successful! Awaiting admin approval.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error uploading the picture.";
    }
} else {
    echo "Invalid request.";
}
?>
