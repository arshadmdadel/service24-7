<?php
// Process the worker registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $nid = $_POST['nid'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $work_type = $_POST['work_type'];
    $detail = $_POST['detail'];
    $price = $_POST['price'];

    // File upload handling
    $picture = $_FILES['picture'];
    $picturePath = 'uploads/' . basename($picture['name']);
    move_uploaded_file($picture['tmp_name'], $picturePath);

    // Save pending registration in a temporary table (e.g., `worker_pending`)
    $conn = new mysqli('localhost', 'root', '', 'your_database');
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("INSERT INTO worker_pending (nid, fullname, phone, email, password, work_type, detail, price, picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssss", $nid, $fullname, $phone, $email, $password, $work_type, $detail, $price, $picturePath);
    if ($stmt->execute()) {
        echo "Registration successful. Waiting for admin approval.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();

    // Redirect admin to notification page
    header("Location: Admin/index.php");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f9;
        }
        form {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        input, select, textarea, button {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <form id="workerRegistration" action="process_worker.php" method="POST" enctype="multipart/form-data">
        <h2>Worker Registration Form</h2>
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="number" name="nid" placeholder="National ID" required>
        <input type="tel" name="phone" placeholder="Phone Number" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="work_type" required>
            <option value="" disabled selected>Select Work Type</option>
            <option value="1">Construction</option>
            <option value="2">Catering</option>
            <option value="3">Cleaning</option>
            <option value="4">Babysitting</option>
            <option value="5">Security</option>
        </select>
        <textarea name="detail" placeholder="Work Details" rows="4" required></textarea>
        <input type="number" name="price" placeholder="Price" required>
        <label for="picture">Upload Picture:</label>
        <input type="file" name="picture" accept="image/*" required>
        <button type="submit">Register</button>
    </form>
</body>
</html>
