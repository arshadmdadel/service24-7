<?php
include('smtp/PHPMailerAutoload.php');

// Function to send OTP
function smtp_mailer($to, $subject, $msg)
{
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = "arshedmdadel@gmail.com"; // Sender's email
    $mail->Password = "xstkkjfyjgynanku"; // App password of the sender email
    $mail->SetFrom("arshedmdadel@gmail.com"); // Sender's email
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => false
        )
    );
    return $mail->Send();
}
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_otp'])) {
    $name = htmlspecialchars($_POST['name']);
    $accountNumber = htmlspecialchars($_POST['accountNumber']);
    $email = htmlspecialchars($_POST['email']);

    $otp = rand(100000, 999999);
    $subject = "Email Verification";
    $emailBody = "Your 6 Digit OTP Code: " . $otp;

    if (smtp_mailer($email, $subject, $emailBody)) {
        session_start();
        $_SESSION['otp'] = $otp;
        $_SESSION['email'] = $email;
        header("Location: verify_otp.php");
        exit;
    } else {
        $error = "Failed to send OTP. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bKash Payment</title>
    <style>
        /* Reset and global styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #e91e63; /* Pink background */
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .payment-container {
            background-color: #fff;
            color: #000;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
            padding: 20px;
        }

        .header {
            margin-bottom: 20px;
        }

        .header img {
            height: 100px;
            width: 240px;
        }

        .merchant-info {
            background-color: #ffe7f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: left;
        }

        .merchant-info span {
            display: block;
            margin-bottom: 5px;
        }

        .form-field {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-field label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #e91e63;
        }

        .form-field input {
            width: 100%;
			height: 3vh;
            /* padding: 10px; */
            border-radius: 5px;
			margin-right: 5px;
            border: 2px solid #ccc;
            font-size: 14px;
        }

        .terms {
            font-size: 12px;
            color: #555;
            margin: 10px 0;
        }

        .terms a {
            color: #e91e63;
            text-decoration: none;
        }

        .terms input {
            margin-right: 5px;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
        }

        .btn {
            width: 48%;
            padding: 10px 0;
            font-size: 14px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn.proceed {
            background-color: #e91e63;
            color: #fff;
        }

        .btn.proceed:hover {
            background-color: #c2185b;
        }

        .btn.close {
            background-color: #ccc;
            color: #000;
        }

        .btn.close:hover {
            background-color: #999;
        }

        .footer {
            font-size: 10px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="header">
            <img src="bkash4.jpg" alt="bKash Logo">
        </div>
        <?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'service24/7');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate a random invoice number
function generateInvoiceNumber() {
    return rand(1000000000, 9999999999); // Generate a 10-digit random number
}

// Generate a unique invoice number
$invoiceNumber = generateInvoiceNumber();

// Ensure the invoice number is unique in the `order` table
$query = "SELECT * FROM `payment` WHERE invoice = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Failed to prepare statement: " . $conn->error);
}

$isUnique = false;
while (!$isUnique) {
    $stmt->bind_param('s', $invoiceNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $isUnique = true; // Invoice number is unique
    } else {
        $invoiceNumber = generateInvoiceNumber(); // Regenerate if not unique
    }

    $result->free();
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Store the invoice number in session or display it directly
$_SESSION['invoiceNumber'] = $invoiceNumber;
?>

<div class="merchant-info">
    <span><strong>Merchant:</strong> Service24/7</span>
    <span><strong>Invoice No:</strong> <?php echo htmlspecialchars($_SESSION['invoiceNumber']); ?></span>
    <span><strong>Order ID:</strong> <?php echo htmlspecialchars($_SESSION['order_id']); ?></span>
    <span><strong>Amount:</strong> <?php echo htmlspecialchars($_SESSION['total_cost']); ?></span>
</div>
        <form method="POST">
            <div class="form-field">
                <label for="accountNumber">Your bKash account number</label>
                <input type="text" id="accountNumber" name="accountNumber" required placeholder="01700000001">
            </div>
			<div class="form-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="terms">
                <input type="checkbox" id="agree" name="agree" required>
                <label for="agree">I agree to the <a href="#">terms and conditions</a></label>
            </div>
            <div class="btn-container">
                <button type="submit" name="send_otp" class="btn proceed">Send otp</button>
                <button type="button" class="btn close" onclick="window.close();">Close</button>
            </div>
        </form>
        <div class="footer">© 16247</div>
    </div>
</body>
</html>

