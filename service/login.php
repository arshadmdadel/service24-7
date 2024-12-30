<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailOrUsername = $_POST['emailOrUsername'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($emailOrUsername) && !empty($password)) {
        // Connect to the database
        $conn = new mysqli('localhost', 'root', '', 'service24/7');

        // Check for connection errors
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Prepare SQL to fetch hashed password by email or username
        $stmt = $conn->prepare("SELECT password FROM user WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
        $stmt->execute();
        $stmt->store_result();

        // Check if a matching record exists
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashedPassword); 
            $stmt->fetch(); 

            // Debugging: Uncomment these to check values
            // echo "Raw Password: $password<br>";
            // echo "Hashed Password from DB: $hashedPassword<br>";

            // Verify the password
            if (password_verify($password, $hashedPassword)) {
                // Login successful - set session variables or redirect
                $_SESSION['user'] = $emailOrUsername; // Save user identifier in the session
                header("Location: home_page.html");
                exit();
            } else {
               
                echo "password_verify($password, $hashedPassword) Invalid password. Please try again.";
            }
        } else {
          
            echo "User not found. Please check your email or username.";
        }


        $stmt->close();
        $conn->close();
    } else {
        echo "Please fill in both fields.";
    }
} else {

    header("Location: login.html");
    exit();
}
