<?php
require 'config.php'; // Include database connection

// Handle deletion of a worker
if (isset($_GET['delete'])) {
    $worker_id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM worker WHERE id = ?");
    $stmt->execute([$worker_id]);
    header("Location: admin.php");
    exit;
}
// Mapping of work types
$work_types = [
    1 => "Electrician",
    2 => "Catering",
    3 => "Cleaning",
    4 => "Babysitting",
    5 => "Security",
];
// Fetch all workers
$stmt = $pdo->prepare("SELECT * FROM worker ORDER BY id DESC");
$stmt->execute();
$workers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Workers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            padding: 15px;
        }

        .card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .card h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .card p {
            color: #555;
            font-size: 14px;
            margin: 5px 0;
        }

        .card .actions {
            margin-top: 10px;
        }

        .card .actions button {
            background-color: #ff4d4f;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .card .actions button:hover {
            background-color: #e43e3e;
        }

        .add-worker {
            display: block;
            text-align: center;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .add-worker:hover {
            background-color: #45a049;
        }
        .rating {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 10px 0;
}

.rating span {
    font-size: 20px;
    color: #ffd700; /* Gold color for stars */
    margin: 0 2px;
}

.rating .inactive {
    color: #ccc; /* Grey color for inactive stars */
}

    </style>
</head>
<body>
    <h1>Manage Workers</h1>
    <a href="add_worker.php" class="add-worker">Add Worker</a>
    <div class="grid">
        <?php foreach ($workers as $worker): ?>
            <div class="card">
                <img src="uploads/<?php echo htmlspecialchars($worker['picture'] ?: 'default.png'); ?>" alt="Worker Picture">
                <h3><?php echo htmlspecialchars($worker['fullname']); ?></h3>
                <p>Work Type: <?php echo htmlspecialchars($work_types[$worker['work_type']] ?? 'Unknown'); ?></p>
                <p>Phone: <?php echo htmlspecialchars($worker['phone']); ?></p>
                <p>Price: tk<?php echo htmlspecialchars($worker['price']); ?></p>
                <div class="rating">
    <span class="<?php echo $worker['rating'] >= 1 ? '' : 'inactive'; ?>">&#9733;</span>
    <span class="<?php echo $worker['rating'] >= 2 ? '' : 'inactive'; ?>">&#9733;</span>
    <span class="<?php echo $worker['rating'] >= 3 ? '' : 'inactive'; ?>">&#9733;</span>
    <span class="<?php echo $worker['rating'] >= 4 ? '' : 'inactive'; ?>">&#9733;</span>
    <span class="<?php echo $worker['rating'] >= 5 ? '' : 'inactive'; ?>">&#9733;</span>
</div>
                <div class="actions">
                    <a href="admin.php?delete=<?php echo $worker['id']; ?>">
                        <button>Remove</button>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
