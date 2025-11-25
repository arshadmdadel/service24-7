<?php
require 'config.php';

// Check if registration details are stored in the session
if (!isset($_SESSION['worker_registration'])) {
    echo "No new registrations.";
    exit;
}

$worker = $_SESSION['worker_registration'];

// Move the uploaded picture to the "uploads" directory
$target_dir = "uploads/";
$target_file = $target_dir . $worker['picture_name'];
if (!move_uploaded_file($worker['picture_tmp'], $target_file)) {
    echo "Error moving the uploaded picture.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept'])) {
        // Save worker data to the database
        $stmt = $conn->prepare("INSERT INTO worker (nid, fullname, phone, email, password, work_type, detail, price, picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssisss", $worker['nid'], $worker['fullname'], $worker['phone'], $worker['email'], $worker['password'], $worker['work_type'], $worker['detail'], $worker['price'], $worker['picture_name']);

        if ($stmt->execute()) {
            echo "Worker registration approved and added to the database.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();

        // Clear the session
        unset($_SESSION['worker_registration']);
    } elseif (isset($_POST['reject'])) {
        echo "Worker registration rejected.";
        unset($_SESSION['worker_registration']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Notification</title>
</head>
<body>
    <h1>New Worker Registration</h1>
    <p><strong>NID:</strong> <?= htmlspecialchars($worker['nid']) ?></p>
    <p><strong>Full Name:</strong> <?= htmlspecialchars($worker['fullname']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($worker['phone']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($worker['email']) ?></p>
    <p><strong>Work Type:</strong> <?= htmlspecialchars($worker['work_type']) ?></p>
    <p><strong>Detail:</strong> <?= htmlspecialchars($worker['detail']) ?></p>
    <p><strong>Price:</strong> <?= htmlspecialchars($worker['price']) ?></p>
    <p><strong>Picture:</strong></p>
    <img src="<?= $target_file ?>" alt="Worker Picture" style="max-width: 300px;"><br>

    <form action="" method="POST">
        <button type="submit" name="accept">Accept</button>
        <button type="submit" name="reject">Reject</button>
    </form>
</body>
</html>
