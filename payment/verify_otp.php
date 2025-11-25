<?php
session_start();
// Establish database connection
$conn = new mysqli('localhost', 'root', '', 'service24/7');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOtp = $_POST['otp'];
    $sessionOtp = $_SESSION['otp'] ?? null;

    if ($enteredOtp == $sessionOtp) {
        // OTP verified successfully
        echo "<script>
            alert('OTP Verified Successfully!\\nOrder ID: {$_SESSION['order_id']}\\nTotal Cost: {$_SESSION['total_cost']}\\nInvoice Number: {$_SESSION['invoiceNumber']}');
        </script>";

        // Clear OTP from session after verification
        unset($_SESSION['otp']);

        // Ensure required session variables are set
        if (isset($_SESSION['order_id'], $_SESSION['total_cost'], $_SESSION['invoiceNumber'])) {
            $orderId = $_SESSION['order_id'];
            $totalCost = $_SESSION['total_cost'];
            $invoiceNumber = $_SESSION['invoiceNumber'];

            // Prepare SQL query to insert payment details
            $query = "INSERT INTO payment (order_id, amount, invoice) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);

            if ($stmt) {
                $stmt->bind_param('isd', $orderId, $totalCost, $invoiceNumber);

                if ($stmt->execute()) {

                    header("Location: ../user_order_page/worker_homepage.php");
                    exit(); 
                } else {
                    // Error during execution
                    echo "<script>alert('Failed to process payment. Please try again.');</script>";
                }

                $stmt->close();
            } else {
                // Error preparing the SQL statement
                echo "<script>alert('Failed to prepare the SQL statement. Please contact support.');</script>";
            }
        } else {
            echo "<script>alert('Required session data is missing. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Invalid OTP. Please try again.');</script>";
    }
}

// Close database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <style>
           /* Reset styles */
           body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #e91e63;
        }
        .header {
            margin-bottom: 20px;
        }

        .header img {
            height: 100px;
            width: 240px;
        }

        .container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
            text-align: center;
            padding: 25px;
            box-sizing: border-box;
        }

        h1 {
            font-size: 24px;
            color: #ff4273;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            font-weight: bold;
            font-size: 14px;
            color: #ff4273;
            display: block;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ffceda;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: #ff4273;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #ff4273;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #cc1e59;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #333;
        }

        .footer a {
            color: #ff4273;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="bkash4.jpg" alt="bKash Logo">
        </div>
        <h1>Verify OTP</h1>
        <form method="POST">
            <div class="form-group">
                <label for="otp">Enter OTP</label>
                <input type="text" id="otp" name="otp" placeholder="Enter the 6-digit OTP" required>
            </div>
            <button type="submit" class="btn">Verify OTP</button>
        </form>
        <div class="footer">
            <p>Need help? <a href="#">Contact Support</a></p>
        </div>
    </div>
</body>
</html>
