<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;
    $amount = $_POST['amount'] ?? null;

    if ($order_id && $amount) {
       
        $_SESSION['order_id'] = $order_id;
        $_SESSION['total_cost'] = $amount;

        
        header("Location: send_otp.php");
        exit();
    } else {
        echo "Invalid data. Please try again.";
    }
} else {
    echo "Invalid request method.";
}
?>
