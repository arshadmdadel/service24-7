<?php
// filepath: c:\xampp\htdocs\service24_today\Admin\admin_login.php
session_start();

$max_attempts = 10;
$lockout_minutes = 5;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $conn = new mysqli('localhost', 'root', '', 'service24/7');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $ip = $_SERVER['REMOTE_ADDR'];

        // Check failed attempts
        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM admin_login_attempts 
            WHERE ip_address = ? 
            AND attempt_time > (NOW() - INTERVAL ? MINUTE)
            AND success = 0
        ");
        $stmt->bind_param("si", $ip, $lockout_minutes);
        $stmt->execute();
        $stmt->bind_result($attempt_count);
        $stmt->fetch();
        $stmt->close();

        if ($attempt_count >= $max_attempts) {
            die("Too many failed login attempts. Please try again after $lockout_minutes minutes.");
        }

        // Fetch admin
// Replace previous SELECT that fetched hash1,hash2,hash3 with password
$stmt = $conn->prepare("SELECT id, username, email, password, role FROM admin WHERE username = ? OR email = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // bind_result changed to read password directly
    $stmt->bind_result($adminId, $admin_username, $email, $db_password, $role);
    $stmt->fetch();

    // Plaintext comparison (no hashing)
    if ($db_password === $password) {
        // Successful login (same as before)
        $delete_stmt = $conn->prepare("DELETE FROM admin_login_attempts WHERE ip_address = ?");
        $delete_stmt->bind_param("s", $ip);
        $delete_stmt->execute();
        $delete_stmt->close();

        // Log successful login
        $log_stmt = $conn->prepare("INSERT INTO admin_login_attempts (ip_address, username, success) VALUES (?, ?, 1)");
        $log_stmt->bind_param("ss", $ip, $admin_username);
        $log_stmt->execute();
        $log_stmt->close();

        // Set session
        $_SESSION['admin_id'] = $adminId;
        $_SESSION['admin_username'] = $admin_username;
        $_SESSION['admin_email'] = $email;
        $_SESSION['admin_role'] = $role;

        // Log activity
        include_once('admin_exfiltration_monitor.php');
        $monitor = new AdminExfiltrationMonitor();
        $monitor->logActivity($adminId, 'login', "Successful login from IP: $ip");

        header("Location: index.php");
        exit();
    } else {
        // Wrong password
        $insert_stmt = $conn->prepare("INSERT INTO admin_login_attempts (ip_address, username, success) VALUES (?, ?, 0)");
        $insert_stmt->bind_param("ss", $ip, $username);
        $insert_stmt->execute();
        $insert_stmt->close();

        $error = "Invalid password. Please try again.";
    }
} else {
    // Admin not found
    $insert_stmt = $conn->prepare("INSERT INTO admin_login_attempts (ip_address, username, success) VALUES (?, ?, 0)");
    $insert_stmt->bind_param("ss", $ip, $username);
    $insert_stmt->execute();
    $insert_stmt->close();

    $error = "Admin not found.";
}

$stmt->close();
$conn->close();
// ...existing code...
    } else {
        $error = "Please fill in both fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Service24/7</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 350px;
        }
        .login-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #5568d3;
        }
        .error {
            background: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2> Admin Login</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Login</button>
        </form>
        
        <div class="info">
            <strong>Test Credentials:</strong><br>
            Primary Admin: primary_admin / Admin@2025<br>
            Major Admin: major_admin / Admin@2025
        </div>
    </div>
</body>
</html>