<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $petType = $_POST['pet_type'] ?? null;
    $price = $_POST['price'] ?? null;

    if ($petType && $price) {
       
        $_SESSION['pet_type'] = $petType;
        $_SESSION['price'] = $price;

        
        header("Location: input_form.php");
        exit();
    } else {
        echo "Invalid data. Please try again.";
    }
} else {
    echo "Invalid request method.";
}
?>
