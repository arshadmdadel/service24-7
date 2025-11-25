<?php
require_once 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo "Access Denied. Please log in as an admin.";
    exit();
}

$result = $conn->query("SELECT * FROM worker_pending");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>New Worker Registrations</h2>
    <?php if (isset($_SESSION['message'])): ?>
        <p style="color: green;"><?= $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">
            <p><strong>Name:</strong> <?= htmlspecialchars($row['fullname']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
            <p><strong>Work Type:</strong> <?= htmlspecialchars($row['work_type']) ?></p>
            <p><strong>Price:</strong> <?= htmlspecialchars($row['price']) ?></p>
            <img src="<?= htmlspecialchars($row['picture']) ?>" alt="Worker Picture" style="width: 100px; height: auto;">
            <form action="approve_worker.php" method="POST" style="display: inline;">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button type="submit">Approve</button>
            </form>
        </div>
    <?php endwhile; ?>
    <?php $conn->close(); ?>
</body>
</html>
