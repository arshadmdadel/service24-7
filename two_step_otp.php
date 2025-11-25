<?php
session_start();
include('payment/smtp/PHPMailerAutoload.php');

// Function to send OTP
function smtp_mailer($to, $subject, $msg) {
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = "arshedmdadel@gmail.com"; // Sender email
    $mail->Password = "xstkkjfyjgynanku"; // App password
    $mail->SetFrom("arshedmdadel@gmail.com");
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

// Check if user is already authenticated from login.php
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user'])) {
    // User should not be here without login
    // header("Location: login.html");
    exit();
}

// Generate OTP
$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;

// Send OTP to user's email
$subject = "Your Verification Code";
$message = "Your OTP is: <b>$otp</b>";

if (smtp_mailer($_SESSION['user'], $subject, $message)) {
    header("Location: verify_otp.php");
    exit();
} else {
    echo "Failed to send OTP. Please try again.";
}
?>
