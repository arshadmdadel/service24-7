<?php

session_start();
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'service24/7';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$start_date = '';
$end_date = '';
$pickup_location = '';
$sort_by = 'rating';
$order = 'ASC';
$result = null;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check_availability'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
    $pickup_location = $_GET['pickup_location'];
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'rating';
    $order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';

    // Validate input
    if (!empty($start_date) && !empty($end_date) && !empty($pickup_location)) {
        // Fetch available workers
        $workers_sql = "
            SELECT w.id, w.fullname, w.rating, w.num_of_rating, w.picture, w.price, w.detail,
                   ((w.rating * w.num_of_rating + 3.5 * 10) / (w.num_of_rating + 10)) AS weighted_rating
            FROM worker w
            LEFT JOIN worker_availability wa ON w.id = wa.worker_id
            WHERE w.work_type = 0
            AND (wa.start_date IS NULL OR wa.end_date IS NULL 
                OR NOT (wa.start_date <= '$end_date' AND wa.end_date >= '$start_date'))
            ORDER BY $sort_by $order
        ";
        $result = $conn->query($workers_sql);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Worker Availability</title>
    <style>
        /* Add CSS here */
        .container {
            min-height:60vh;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .input-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .input-form input, .input-form button {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            font-size: 14px;
        }

        .input-form button {
            background-color: #099468;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .input-form button:hover {
            background-color: green;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
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
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .card p {
            margin: 5px 0;
        }

        .card button {
            background-color: #ff5722;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .card button:hover {
            background-color: #e64a19;
        }

        .star-rating .star {
    font-size: 20px;
    color: #ccc; /* Default color for empty stars */
}

.star-rating .star.full {
    color: #ffd700; /* Gold for full stars */
}

.star-rating .star.half::before {
    content: '\2605'; /* Unicode for filled star */
    color: #ffd700; /* Gold for half stars */
    position: absolute;
    clip-path: inset(0 50% 0 0); /* Half star effect */
}

    </style>
</head>
<body>

<div class="container">
    <!-- Input Form -->
    <div class="input-form">
        <form method="get">
            <h2>Check Worker Availability</h2>
            <input type="date" name="start_date" value="<?php echo $start_date; ?>" required>
            <input type="date" name="end_date" value="<?php echo $end_date; ?>" required>
            <input type="text" name="pickup_location" placeholder="Enter Pickup Location" value="<?php echo $pickup_location; ?>" required>
            <div class="sort-options">
                <select name="sort_by">
                    <option value="rating" <?php echo $sort_by === 'rating' ? 'selected' : ''; ?>>Sort by Rating</option>
                    <option value="price" <?php echo $sort_by === 'price' ? 'selected' : ''; ?>>Sort by Price</option>
                </select>
                <select name="order">
                    <option value="asc" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                    <option value="desc" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                </select>
            </div>
            <button type="submit" name="check_availability">Check Availability</button>
        </form>
    </div>

    <!-- Available Workers -->
    <div class="card-grid">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Fetch worker information
                $worker_id = $row['id'];
                $worker_fullname = $row['fullname'];
                $worker_rating = $row['rating'];
                $worker_price = $row['price'];
                $worker_img=$row['picture']
                ?>
                
                <?php
if (!function_exists('generateStarRating')) {
    function generateStarRating($rating) {
        $output = '<div class="star-rating" id="starRating">';
        for ($i = 1; $i <= 5; $i++) {
            if ($rating >= $i) {
                // Full star
                $output .= '<span class="star full" data-value="' . $i . '">&#9733;</span>';
            } elseif ($rating > $i - 1 && $rating < $i) {
                // Half star
                $output .= '<span class="star half" data-value="' . $i . '">&#9733;</span>';
            } else {
                // Empty star
                $output .= '<span class="star empty" data-value="' . $i . '">&#9734;</span>';
            }
        }
        $output .= '</div>';
        return $output;
    }
}
?>


<!-- Pass worker details, date, and location via hidden inputs -->
<form method="post" action="handle_data.php">
    <div class="card">
    <img src="../RAF/uploads/<?php echo htmlspecialchars($worker_img ?: 'default.png'); ?>" alt="Worker Picture">
        <h3><?php echo $worker_fullname; ?></h3>
        
        <!-- Star Rating -->
        <p><strong>Rating:</strong> <div style="display: inline-flex; align-items: center;">
    <?php echo generateStarRating($worker_rating); ?>
    <!-- <span style="margin-left: 5px;"><?php echo $worker_rating; ?></span> -->
</div>
</p>
        
        <p><strong>Price:</strong> ৳<?php echo $worker_price; ?></p>
        <p><?php echo $row['detail']; ?></p>

        <!-- Hidden fields to pass additional information -->
        <input type="hidden" name="worker_id" value="<?php echo $worker_id; ?>">
        <input type="hidden" name="start_date" value="<?php echo $start_date; ?>">
        <input type="hidden" name="end_date" value="<?php echo $end_date; ?>">
        <input type="hidden" name="pickup_location" value="<?php echo $pickup_location; ?>">

        <button type="submit" class="card-button">Select Worker</button>
    </div>
</form>

                <?php
            }
        } else {
            echo "";
        }
        ?>
    </div>
</div>

<?php include("../footer.php") ?>
</body>
</html>

<?php

?>
