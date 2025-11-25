<?php
if (isset($_GET['worker_id'])) {
    $worker_id = intval($_GET['worker_id']);

    $host = 'localhost'; 
    $username = 'root';
    $password = ''; 
    $database = 'service24/7'; 

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM worker WHERE id = $worker_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $worker = $result->fetch_assoc();
    } else {
        die("<p>Worker not found. Please go back and select a worker.</p>");
    }

    $conn->close();
} else {
    die("<p>No worker selected. Please go back and select a worker.</p>");
}

session_start();
$cart = $_SESSION['cart'] ?? [];
$items = [
    'Item1' => 5,
    'Item2' => 8,
    'Item3' => 10,
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Page</title>
    <style>
          *{
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* <!-- <HEADER> --> */
        header{
            height: 10vh;
            width: 100vw;
            background-color: #fdf5ea;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            
        }
        a{
            text-decoration: none;

        }
        .A{
            color: black;
        }

        a:hover{
            color: #099468;
        }

        /* <!-- <HEADER> --> */



        /* <!-- <FOOTER> --> */

        footer{
            height: 30vh;
            width: 100vw;
            background-color: #111D15;
            display: grid;
            align-items: center;
            justify-content: center;
            color: white;
            
            
        }

        #content{
            height: 20vh;
            width: 90vw;
            margin-left: 150px;
            display: flex;
            align-items: center;
            justify-content: space-evenly;
            color: white;
        }

        #foot1{
            height: 15vh;
            width: 20vw;
        }

        #foot2{
            height: 15vh;
            width: 10vw;
            display: grid;
            align-items: center;
            justify-content: center;
            color: white;
        }

        #foot3{
            height: 15vh;
            width: 15vw;
            display: grid;
            align-items: center;
            justify-content: center;
            color: white;
        }

        #foot5{
            width: 100vw;
            text-align: center;
        }

        hr {
            border: 1px solid white;
            width: 95%;
            margin-left: 45px;
        }

        /* <!-- <FOOTER> --> */

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f8fa;
        }

        .container {
            width: 1200px;
            margin: 20px auto;
            display: flex;
            gap: 20px;
        }

        #left, #right {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        #left {
            flex: 1;
        }

        #right {
            flex: 0.6;
        }

        h1, h2 {
            color: #2b7a4b;
            margin-bottom: 10px;
        }

        h3 {
            margin: 10px 0;
            color: #333;
        }

        p {
            color: #555;
            margin: 5px 0;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
        }

        .input-group input {
            width: 96%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .order-summary {
            margin-top: 20px;
        }

        .order-summary .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .order-summary .item:last-child {
            font-weight: bold;
        }

        .button {
            background-color: #099468;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
        }

        .button:hover {
            background-color: #086f52;
        }
    </style>
</head>
<body>
    <!-- <HEADER> -->
    <header>
        <div style="font-size: 37px; font-weight: 400; color: #099468; margin-left: 10px;">
            <p style="text-shadow: 5px 5px 5px 5px rgba(0,0,0,0.5);">Service24/7</p>
        </div>
        <div
            style="font-size: 20px; font-weight: 200;  width: 30vw; display: flex; align-items: center; justify-content: space-between;">
            <a href="" class="A">Home</a>
            <a href="" class="A">Review</a>
            <a href="" class="A">Contact</a>
            <select style="border: none; font-size: 20px; background-color: #fff; color: #099468; font-family: 'Times New Roman', Times, serif;"
            name="service" id="serviceSelect" onchange="navigateToPage(this.value)">
            <option class="Service" value="" selected disabled>Service</option>
            <option class="Service" value="cleaning.html">Cleaning</option>
            <option class="Service" value="catering.html">Catering</option>
            <option class="Service" value="electrician.html">Electrician</option>
            <option class="Service" value="security.html">Security</option>
            <option class="Service" value="babysitting.html">Baby Sitting</option>
            <option class="Service" value="petcaring/pet1.html">Pet Caring</option>
        </select>
        
        <script>
            function navigateToPage(value) {
                if (value) {
                    window.location.href = value;
                }
            }
        </script>
        
        </div>
        <div style="display: flex; align-items: center; justify-content: center;">
            <div><img src="../image/notification.png" alt="" ></div>
            <div style="padding-left: 35px;">
                <a href="login.html">
                    <img src="../image/profile.png" alt="Profile">
                </a>
            </div>
            

        </div>
    </header>

    <!-- <HEADER> -->

    <div class="container">
        <!-- Left Box -->
        <div id="left">
            <h1>Place Your Order</h1>
            <form method="POST" action="submit_order.php">
                <div class="input-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="input-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" required>
                </div>
                <div class="input-group">
                    <label for="date">Preferred Date</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <button type="submit" class="button">Submit Order</button>
            </form>
        </div>

        <!-- Right Box -->
        <div id="right">
            <h2>Order Summary</h2>
            <h3>Worker Details</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($worker['fullname']) ?></p>
            <p><strong>Rating:</strong> <?= htmlspecialchars($worker['rating']) ?></p>
            <p><strong>Details:</strong> <?= htmlspecialchars($worker['detail']) ?></p>
            <div class="order-summary">
                <h3>Items</h3>
                <?php
                $total = 0;

                //items
                foreach ($cart as $item) {
                    $itemName = $item['item'];
                    $quantity = $item['quantity'];
                    $price = $items[$itemName] * $quantity;
                    $total += $price;

                    echo "<div class='item'>
                            <span>{$itemName} (x{$quantity})</span>
                            <span>\${$price}</span>
                          </div>";
                }

                // worker
                $workerPrice = $worker['price'];
                $total += $workerPrice;

                echo "<div class='item'>
                        <span>Worker Fee</span>
                        <span>\${$workerPrice}</span>
                      </div>";

                echo "<div class='item'>
                        <span>Total</span>
                        <span>\${$total}</span>
                      </div>";
                ?>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <footer>
        <div id="content">
            <div id="foot1">
                <div style="display: flex;align-items: center;">
                    <img src="clean.png" alt="">
                    <span style="font-size: 30px; font-weight: bold;">Service24/7</span>
                </div>
                <p style="font-size: 20px; margin-top: 15px;">Stay updated with our latest tips, service updates,
                    and helpful articles on maintaining a spotless home.</p>
            </div>
            <div id="foot2">
                <h3>Company</h3>
                <a href="#">About Us</a>
                <a href="#">Services</a>
                <a href="#">Our Team</a>
            </div>
            <div id="foot3">
                <h3>Know More</h3>
                <a href="#">Support</a>
                <a href="#">Privacy</a>
                <a href="#">Terms and Conditions</a>
            </div>
        </div>
        <hr>
        <div id="foot5">
            <p>2024 &copy; "Procleaning" All Rights Reserved</p>
        </div>
    </footer>
</body>
</html>
