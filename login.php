<?php
session_start();

$max_attempts = 5;       
$lockout_minutes = 1;    

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailOrUsername = $_POST['emailOrUsername'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($emailOrUsername) && !empty($password)) {
        
        $conn = new mysqli('localhost', 'root', '', 'service24/7');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $ip = $_SERVER['REMOTE_ADDR'];

        // Check failed attempts in last X minutes
        $stmt = $conn->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? 
                                AND attempt_time > (NOW() - INTERVAL ? MINUTE)");
        $stmt->bind_param("si", $ip, $lockout_minutes);
        $stmt->execute();
        $stmt->bind_result($attempt_count);
        $stmt->fetch();
        $stmt->close();

        if ($attempt_count >= $max_attempts) {
            die("Too many failed login attempts. Please try again after $lockout_minutes minutes.");
        }

        $stmt = $conn->prepare("SELECT id, email, hash1, hash2, hash3 FROM user WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($userId, $email, $db_hash1, $db_hash2, $db_hash3); 
            $stmt->fetch(); 

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

            if ($db_hash1 == $hash1 && $db_hash2 == $hash2 && $db_hash3 == $hash3) {
                $delete_stmt = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
                $delete_stmt->bind_param("s", $ip);
                $delete_stmt->execute();
                $delete_stmt->close();

                // Start session
                $_SESSION['user_id'] = $userId;        
                $_SESSION['user'] = $emailOrUsername;  
                $_SESSION['email'] = $email;           
                header("Location: two_step_otp.php");  // redirect to OTP
                exit();
            } else {
                // Wrong password
                $insert_stmt = $conn->prepare("INSERT INTO login_attempts (ip_address, email_or_username) VALUES (?, ?)");
                $insert_stmt->bind_param("ss", $ip, $emailOrUsername);
                $insert_stmt->execute();
                $insert_stmt->close();

                echo "Invalid password. Please try again.";
            }
        } else {
            // User not found
            $insert_stmt = $conn->prepare("INSERT INTO login_attempts (ip_address, email_or_username) VALUES (?, ?)");
            $insert_stmt->bind_param("ss", $ip, $emailOrUsername);
            $insert_stmt->execute();
            $insert_stmt->close();

            echo "User not found. Please check your email or username.";
        }

        // Logging execution time
        $start_time = microtime(true);
        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;

        $log_message = "Login Execution Time: " . number_format($execution_time, 4) . " seconds\n";
        file_put_contents('performance_log.txt', $log_message, FILE_APPEND);

        $stmt->close();
        $conn->close();
    } else {
        echo "Please fill in both fields.";
    }
} else {
    header("Location: login.html");
    exit();
}
?>
