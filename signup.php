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
        } else {
            echo "Connected successfully";
        }

        // Rolling Hash Function
        function rolling_hash($str, $base, $mod) {
            $hash = 0;
            for ($i = 0; $i < strlen($str); $i++) {
                $hash = ($hash * $base + ord($str[$i])) % $mod;
            }
            return $hash;
        }

        $hash1 = rolling_hash($password, 31, 1000000007);
        $hash2 = rolling_hash($password, 37, 1000000009);
        $hash3 = rolling_hash($password, 41, 1000000021);

        $stmt = $conn->prepare("INSERT INTO user (fullName, username, email, hash1, hash2, hash3) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiii", $fullName, $username, $email, $hash1, $hash2, $hash3);

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