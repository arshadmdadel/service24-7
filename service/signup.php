<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['fullName'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $terms = isset($_POST['terms']);

    if ($terms && !empty($fullName) && !empty($username) && !empty($email) && !empty($password)) {
        $conn = new mysqli('localhost', 'root', '', 'service24/7');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }else{
            echo "Connected successfully";
        }

        //hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        //insert data
        $stmt = $conn->prepare("INSERT INTO user (fullName, username, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullName, $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            echo "Signup successful!";
            header('location:login.html');
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Please fill in all fields and agree to the terms.";
    }
}
?>