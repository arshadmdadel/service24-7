<?php
$host = 'localhost'; 
$username = 'root'; 
$password = '';     
$database = 'service24/7'; 

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'rating'; 
$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC'; 

$C = 3.5; 
$M = 10;  

$sql = "
    SELECT 
        *,
        (
            (rating * num_of_rating + $C * $M) / (num_of_rating + $M)
        ) AS weighted_rating
    FROM worker
    WHERE work_type = 1
    ORDER BY " . ($sort_by === 'price' ? 'price' : 'weighted_rating') . " $order
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Filter</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f8fa;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .sort-options {
            display: flex;
            gap: 10px;
        }

        .sort-options select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            cursor: pointer;
            font-size: 14px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            font-size: 14px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
        }

        .card img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin: auto;
            margin-bottom: 10px;
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .card p {
            margin: 5px 0;
            color: #555;
        }

        .card .detail {
            color: #777;
            font-size: 13px;
            margin: 10px 0;
            font-style: italic;
        }

        .card button {
            background-color: #ff5722;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }

        .card button:hover {
            background-color: #e64a19;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Worker List</h1>
            <div class="sort-options">
                <form method="get" style="display: flex; gap: 10px;">
                    <select name="sort_by">
                        <option value="rating" <?php echo $sort_by === 'rating' ? 'selected' : ''; ?>>Sort by Rating</option>
                        <option value="price" <?php echo $sort_by === 'price' ? 'selected' : ''; ?>>Sort by Price</option>
                    </select>
                    <select name="order">
                        <option value="asc" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                        <option value="desc" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                    </select>
                    <button type="submit">Apply</button>
                </form>
            </div>
        </div>

        <div class="card-grid">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card'>
                            <img src='https://via.placeholder.com/60' alt='Profile Picture'>
                            <h3>{$row['fullname']}</h3>
                            <p><strong>Rating:</strong> {$row['rating']}</p>
                            <p><strong>Number of Ratings:</strong> {$row['num_of_rating']}</p>
                            <p><strong>Price:</strong> \${$row['price']}</p>
                            <p class='detail'>{$row['detail']}</p>
                            <form action='order.php' method='get'>
                                <button type='submit' name='worker_id' value='{$row['id']}'>Add Worker</button>
                            </form>
                          </div>";
                }
            } else {
                echo "<p>No workers found</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
