<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'service24/7');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$query = "SELECT p.id AS payment_id, p.invoice, p.amount, o.service_name, o.time 
          FROM payment p 
          JOIN `order` o ON p.order_id = o.id 
          WHERE o.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment History</title>
</head>
<body>
    <h1>Payment History</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Service</th>
                <th>Time</th>
                <th>Invoice</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['payment_id']); ?></td>
                <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                <td><?php echo htmlspecialchars($row['time']); ?></td>
                <td><?php echo htmlspecialchars($row['invoice']); ?></td>
                <td>$<?php echo htmlspecialchars($row['amount']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
