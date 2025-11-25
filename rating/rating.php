<?php
// PHP to include the database connection
$servername = "localhost";
$username = "root"; // Update with your database username
$password = ""; // Update with your database password
$dbname = "service24/7"; // Update with your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['orderID'] ?? null;

        $_SESSION['orderID'] = $order_id;


} else {
    echo "Invalid request method.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            display: flex;
            width: 80%;
            max-width: 1200px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .left {
            flex: 1;
            padding: 2rem;
        }

        .right {
            flex: 1;
            background-color: #eaf4ed;
        }

        .title {
            font-size: 2rem;
            color: #2c7a5b;
            margin-bottom: 1rem;
        }

        .star-rating {
            display: flex;
            gap: 0.5rem;
            margin: 1rem 0;
            cursor: pointer;
        }

        .star {
            font-size: 2rem;
            color: #b5c1b5;
            transition: color 0.3s;
        }

        .star.selected {
            color: #2c7a5b;
        }

        textarea {
            width: 100%;
            height: 100px;
            border: 1px solid #cfd8cf;
            border-radius: 4px;
            padding: 1rem;
            resize: none;
        }

        button {
            margin-top: 1rem;
            padding: 0.8rem 2rem;
            background-color: #2c7a5b;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #235e46;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <div class="title">Submit Review</div>
            <form action="submit_review.php" method="POST">
                <div class="star-rating" id="starRating">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
                <input type="hidden" name="rating" id="rating" value="0">
                <textarea name="comment" placeholder="Write your review here..."></textarea>
                <button type="submit">Submit</button>
            </form>
        </div>
        <div class="right">
            <img src="review.jpg" alt="">
        </div>
    </div>

    <script>
        // Handle star selection
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating');
        stars.forEach(star => {
            star.addEventListener('mouseover', () => {
                const value = star.getAttribute('data-value');
                updateStars(value);
            });

            star.addEventListener('mouseout', () => {
                const selectedRating = ratingInput.value;
                updateStars(selectedRating);
            });

            star.addEventListener('click', () => {
                const value = star.getAttribute('data-value');
                ratingInput.value = value;
            });
        });

        function updateStars(value) {
            stars.forEach(star => {
                if (parseInt(star.getAttribute('data-value')) <= value) {
                    star.classList.add('selected');
                } else {
                    star.classList.remove('selected');
                }
            });
        }
    </script>
</body>
</html>
