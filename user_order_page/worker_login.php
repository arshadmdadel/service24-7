<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $workerID = $_POST['workerID'] ?? '';
    $workerFullName = $_POST['workerFullName'] ?? '';
    $workerPassword = $_POST['workerPassword'] ?? '';

    if (!empty($workerID) && !empty($workerFullName) && !empty($workerPassword)) {
        $conn = new mysqli('localhost', 'root', '', 'service24/7');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT * FROM worker WHERE id = ? AND fullname = ? AND password = ?");
        $stmt->bind_param("sss", $workerID, $workerFullName, $workerPassword);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['worker_id'] = $workerID;
            $_SESSION['worker_name'] = $workerFullName;
            header("Location: worker_homepage.php ");
            exit();
        } else {
            echo "<h1>Invalid Credentials </h1>";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "<h1>Please fill in all fields.</h1>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Login</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #A5D6A7, #81C784);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .container {
            display: flex;
            width: 60%;
            max-width: 800px;
            background: #FFFFFF;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            overflow: hidden;
        }

        .left-section {
            background: #66BB6A;
            color: white;
            padding: 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .left-section h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        .left-section p {
            font-size: 16px;
            line-height: 1.5;
        }

        .right-section {
            flex: 2;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #E8F5E9;
        }

        .right-section h2 {
            font-size: 32px;
            margin-bottom: 20px;
            text-align: center;
            color: #388E3C;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input:focus {
            border-color: #66BB6A;
            box-shadow: 0 0 8px rgba(102, 187, 106, 0.5);
        }

        .button-container {
            text-align: center;
        }

        button {
            padding: 12px 20px;
            background: #388E3C;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            background: #66BB6A;
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        p {
            text-align: center;
            font-size: 14px;
            color: #555;
        }

        p a {
            color: #388E3C;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        p a:hover {
            color: #66BB6A;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <h2>Welcome Back</h2>
            <p>Login to manage your tasks and access your dashboard.</p>
        </div>
        <div class="right-section">
            <h2>Worker Login</h2>
            <form id="workerLoginForm" action="worker_login.php" method="POST">
                <input type="text" id="workerID" name="workerID" placeholder="Worker ID" required>
                <input type="text" id="workerFullName" name="workerFullName" placeholder="Full Name" required>
                <input type="password" id="workerPassword" name="workerPassword" placeholder="Password" required>
                <div class="button-container">
                    <button type="submit">Log In</button>
                </div>
                <p>Not a worker? <a href="index.html">Go Back</a></p>
            </form>
        </div>
    </div>
</body>
</html>

