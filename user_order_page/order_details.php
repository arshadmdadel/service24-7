<?php
session_start();
$userId = $_SESSION['user_id'] ?? null; // Ensure user_id is set

if (!$userId) {
    die("User is not logged in.");
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'service24/7');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch order details
$orderId = $_POST['orderID'] ?? null; // Ensure orderID is passed
$serviceName=$_POST['service_name'] ?? null;
$accept_status=$_POST['accept_status'] ?? null;
if (!$orderId) {
    die("Order ID is not provided.");
}

if ($serviceName == "Pet Caring" || $serviceName=="Electrician") {
    
    $stmt = $conn->prepare("
    SELECT 
        o.id AS order_id,
        o.time,
        o.service_name,
        w.fullname AS worker_name,
        w.picture AS worker_picture,
        w.nid AS worker_nid,
        w.phone AS worker_phone,
        w.email AS worker_email,
        w.work_type AS worker_work_type,
        w.detail AS worker_detail,
        w.rating AS worker_rating,
        w.num_of_rating AS worker_num_of_rating,
        w.price AS worker_price,
        pc.pet_type,
        pc.place_price,
        pc.start_date,
        pc.end_date,
        pc.pickup_location,
        pc.total_cost
    FROM `order` o
    LEFT JOIN `worker` w ON o.worker_id = w.id
    LEFT JOIN `pet_caring` pc ON o.id = pc.order_id
    WHERE o.id = ?
");

}

elseif ($serviceName == "Baby Sitting") {
    
    $stmt = $conn->prepare("
    SELECT 
        o.id AS order_id,
        o.time,
        o.service_name,
        w.fullname AS worker_name,
        w.nid AS worker_nid,
        w.picture AS worker_picture,
        w.phone AS worker_phone,
        w.email AS worker_email,
        w.work_type AS worker_work_type,
        w.detail AS worker_detail,
        w.rating AS worker_rating,
        w.num_of_rating AS worker_num_of_rating,
        w.price AS worker_price,
        bs.age_group,
        bs.place_price,
        bs.start_date,
        bs.end_date,
        bs.pickup_location,
        bs.total_cost
    FROM `order` o
    LEFT JOIN `worker` w ON o.worker_id = w.id
    LEFT JOIN `baby_sitting` bs ON o.id = bs.order_id
    WHERE o.id = ?
");
}

elseif ($serviceName === "Catering") {
    // Query for catering service
    $stmt = $conn->prepare("
        SELECT 
            o.id AS order_id,
            o.time,
            o.service_name,
            w.fullname AS worker_name,
            w.picture AS worker_picture,
            w.nid AS worker_nid,
            w.phone AS worker_phone,
            w.email AS worker_email,
            w.work_type AS worker_work_type,
            w.detail AS worker_detail,
            w.rating AS worker_rating,
            w.num_of_rating AS worker_num_of_rating,
            w.price AS worker_price,
            c.item1, c.item2, c.item3, c.item4, c.item5, 
            c.item6, c.item7, c.item8, c.item9, c.item10, 
            c.item11, c.total_price AS total_cost
        FROM `order` o
        LEFT JOIN `worker` w ON o.worker_id = w.id
        LEFT JOIN `catering` c ON o.id = c.order_id
        WHERE o.id = ?
    ");
} 

else {
    
    $stmt = $conn->prepare("
    SELECT 
        o.id AS order_id,
        o.time,
        o.service_name,
        w.fullname AS worker_name,
        w.nid AS worker_nid,
        w.picture AS worker_picture,
        w.phone AS worker_phone,
        w.email AS worker_email,
        w.work_type AS worker_work_type,
        w.detail AS worker_detail,
        w.rating AS worker_rating,
        w.num_of_rating AS worker_num_of_rating,
        w.price AS worker_price,
        pc.pet_type,
        pc.place_price,
        pc.start_date,
        pc.end_date,
        pc.pickup_location,
        pc.total_cost
    FROM `order` o
    LEFT JOIN `worker` w ON o.worker_id = w.id
    LEFT JOIN `pet_caring` pc ON o.id = pc.order_id
    WHERE o.id = ?
");
}

$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("No order details found for the given ID.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <style>

                /* <!-- <HEADER> --> */
*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    overflow-x: hidden;
}

header {
    height: 10vh;
    width: 100vw;
    background-color: white;
    display: flex;
    justify-content: space-evenly;
    align-items: center;
}

a {
    text-decoration: none;
    position: relative; /* Necessary for pseudo-element positioning */
    color: black;
    padding: 5px 0;
}

a:hover {
    color: #099468;
}

a::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: 0;
    height: 2px;
    width: 0%;
    background-color: #099468;
    transition: width 0.3s ease; /* Smooth animation */
}

a:hover::after {
    width: 100%; /* Expands the underline */
}

.Service {
    height: 7vh;
    width: 15vw;
    border: none;
    padding: 5px;
    background: white;
}

/* select:hover,
.Service:hover {
    color: #099468;
    border-bottom: 2px solid #099468; /* Green underline for the select element */
/* } */ 

select {
    outline: none; /* Removes the default outline for better visuals */
}


/* <!-- <HEADER> --> */

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fb;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px 30px;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid #e4e6eb;
            padding-bottom: 20px;
        }

        .header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }

        .header-details h1 {
            font-size: 1.5rem;
            color: #333;
            margin: 0;
        }

        .header-details p {
            font-size: 0.9rem;
            color: #666;
            margin: 5px 0;
        }

        .order-info {
            margin: 20px 0;
        }

        .section-title {
            font-size: 1rem;
            color: #555;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .details-list p {
            font-size: 0.9rem;
            color: #444;
            margin: 5px 0;
        }

        .details-list span {
            font-weight: bold;
            color: #555;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding: 10px 0;
            border-top: 1px solid #e4e6eb;
        }

        .summary div {
            font-size: 0.9rem;
            color: #444;
        }

        .summary .total {
            font-weight: bold;
            font-size: 1.2rem;
            color: #222;
        }

        .help-links {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .help-links a {
            text-decoration: none;
            color: #007bff;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .help-links a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .header img {
                margin-bottom: 15px;
            }

            .summary {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }

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
.flink{
    text-decoration: none;
    position: relative; 
    color: #fff;
    padding: 5px 0;
}

#fhr {
    border: 1px solid white; 
    width: 95%;
    margin-left: 45px;
}

/* <!-- <FOOTER> --> */
    </style>
</head>
<body>
<?php include("../header.php") ?>

    <div class="container">
        <div class="header">
        <img src="../RAF/uploads/<?php echo htmlspecialchars($order['worker_picture'] ?: 'default.png'); ?>" alt="Worker Picture">
            <div class="header-details">
                <h1><?php echo htmlspecialchars($order['worker_name']); ?></h1>
                <p>NID: <?php echo htmlspecialchars($order['worker_nid']); ?> </p>
                <p><strong>Worker price:</strong> <?php echo htmlspecialchars($order['worker_price']); ?></p>
            </div>
            <div style="margin-left:auto; text-align:right;">
                <!-- <p><strong><?php echo htmlspecialchars($order['worker_price']); ?></strong></p> -->
            </div>
        </div>
        <div class="order-info">
            <div class="section-title">Order Summary</div>
            <div class="details-list">
                <p><span>Order ID:</span> <?php echo htmlspecialchars($order['order_id']); ?></p>
                <p><span>Service:</span> <?php echo htmlspecialchars($serviceName); ?></p>
                <p><span>Order Time:</span> <?php echo htmlspecialchars($order['time']); ?></p>
                
            </div>
        </div>
        <?php if ($order['worker_work_type'] == 0): ?>
    <div class="order-info">
        <div class="section-title">Details</div>
        <div class="details-list">
            <p><span>Type:</span> <?php echo htmlspecialchars($order['pet_type']); ?></p>
            <p><span>Place Price:</span> ৳<?php echo htmlspecialchars($order['place_price']); ?></p>
            <p><span>Start Date:</span> <?php echo htmlspecialchars($order['start_date']); ?></p>
            <p><span>End Date:</span> <?php echo htmlspecialchars($order['end_date']); ?></p>
            <p><span>Pickup Location:</span> <?php echo htmlspecialchars($order['pickup_location']); ?></p>
            <p><span>Total Cost:</span> ৳<?php echo htmlspecialchars($order['total_cost']); ?></p>
        </div>
    </div>

    <?php elseif ($order['worker_work_type'] == 4): ?>
    <div class="order-info">
        <div class="section-title">Baby Sitting Details</div>
        <div class="details-list">
            <p><span>Age Group:</span> <?php echo htmlspecialchars($order['age_group']); ?></p>
            <p><span>Place Price:</span> ৳<?php echo htmlspecialchars($order['place_price']); ?></p>
            <p><span>Start Date:</span> <?php echo htmlspecialchars($order['start_date']); ?></p>
            <p><span>End Date:</span> <?php echo htmlspecialchars($order['end_date']); ?></p>
            <p><span>Pickup Location:</span> <?php echo htmlspecialchars($order['pickup_location']); ?></p>
            <p><span>Total Cost:</span> ৳<?php echo htmlspecialchars($order['total_cost']); ?></p>
        </div>
    </div>

    <?php elseif ($order['worker_work_type'] == 2): ?>
    <div class="order-info">
        <div class="section-title">Catering Order Details</div>
        <div class="details-list">
            <p><span>Worker Name:</span> <?php echo htmlspecialchars($order['worker_name']); ?></p>
            <p><span>Service Time:</span> <?php echo htmlspecialchars($order['time']); ?></p>
            <p><span>Total Price:</span> ৳<?php echo htmlspecialchars($order['total_cost']); ?></p>
            
            <div class="section-title">Food Items:</div>
<ul>
    <?php 
    $items = [
        ['name' => 'Morog Polaw', 'price' => 600, 'image' => 'ban1.jpg'],
        ['name' => 'Kachi Biriani', 'price' => 800, 'image' => 'ban2.jpg'],
        ['name' => 'Khichuri & Bhuna Beaf', 'price' => 900, 'image' => 'ban3.jpg'],
        ['name' => 'Steak & Eggs', 'price' => 1000, 'image' => 'west1.jpg'],
        ['name' => 'Burger', 'price' => 450, 'image' => 'west2.jpg'],
        ['name' => 'Pasta', 'price' => 500, 'image' => 'west3.jpg'],
        ['name' => 'Fried rice', 'price' => 550, 'image' => 'chi1.jpg'],
        ['name' => 'Full bowl ramen', 'price' => 600, 'image' => 'chi2.jpg'],
        ['name' => 'Hot pot', 'price' => 1000, 'image' => 'chi3.jpg'],
        ['name' => 'Mojo(2Lt)', 'price' => 100, 'image' => 'mojo.jpg'],
        ['name' => 'Borhani(2Lt)', 'price' => 200, 'image' => 'borhani.jpg'],
    ];

    for ($i = 1; $i <= 11; $i++): 
        if (!empty($order["item{$i}"])): 
            $itemIndex = $i - 1; // Adjust index to match the array (0-based index)
            $item = $items[$itemIndex];
    ?>
        <li>
            <div class="food-item">
                <img src="path/to/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" width="100" height="100">
                <div class="food-details">
                    <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                    <span>Price: ৳<?php echo htmlspecialchars($item['price']); ?></span><br>
                    <span>Quantity: <?php echo htmlspecialchars($order["item{$i}"]); ?></span>
                </div>
            </div>
        </li>
    <?php 
        endif; 
    endfor; 
    ?>
</ul>

        </div>
    </div>



<?php else: ?>
    <div class="order-info">
    <div class="section-title">Details</div>
        <div class="details-list">
            <p><span>Type:</span> <?php echo htmlspecialchars($order['pet_type']); ?></p>
            <p><span>Place Price:</span> ৳<?php echo htmlspecialchars($order['place_price']); ?></p>
            <p><span>Start Date:</span> <?php echo htmlspecialchars($order['start_date']); ?></p>
            <p><span>End Date:</span> <?php echo htmlspecialchars($order['end_date']); ?></p>
            <p><span>Pickup Location:</span> <?php echo htmlspecialchars($order['pickup_location']); ?></p>
            <p><span>Total Cost:</span> ৳<?php echo htmlspecialchars($order['total_cost']); ?></p>
        </div>
    </div>
<?php endif; ?>

        <div class="help-links">
    <!-- <a href="#">Service Issues</a> -->

    <?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'service24/7');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the order ID exists in the payment table
$query = "SELECT * FROM payment WHERE order_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Failed to prepare statement: " . $conn->error);
}

$stmt->bind_param('i', $orderId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Failed to execute query: " . $stmt->error);
}

if ($accept_status == 0): ?>
    <span>Please wait for the order to be accepted.</span>
<?php elseif ($result->num_rows == 0 && $accept_status == 1): ?>
    <form action="../payment/process.php" method="POST" style="display: inline;">
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($orderId); ?>">
        <input type="hidden" name="amount" value="<?php echo htmlspecialchars($order['total_cost']); ?>">
        <button type="submit" style="text-decoration: none; background: none; border: none; color: blue; cursor: pointer;">
           Go for Payment
        </button>
    </form>
<?php else: ?>
    <a href="payment_history.php">Payment is completed</a>
<?php endif; ?>

</div>
    </div>

     <!-- <FOOTER> -->
     <footer>

<div id="content">

    <div id="foot1">
        <div style="display: flex;align-items: center;">
            <img src="../image/clean.png" alt="">
            <span style="font-size: 30px; font-weight: bold;">Service24/7</span>
        </div>
        <p style="font-size: 20px; margin-top: 15px;">Stay updated with our latest tips, service updates,
            and
            helpful articles on maintaining a spotless home.</p>
    </div>
        <div id="foot2">
            <h3>Company</h3>
            <a class="flink">About Us</a>
            <a  class="flink">Services</a>
            <a  class="flink">Our Team</a>
        </div>
        <div id="foot3">
            <h3  class="flink">Know More</h3>
            <a class="flink">Support</a>
            <a class="flink">Privacy</a>
            <a class="flink">Terms and Condition</a>
        </div>
    <div id="foot4">

    </div>

</div>

<hr id="fhr">

<div id="foot5">
    <p>2024 “Procleaning” All Rights Received</p>
</div>
</footer>

<!-- <FOOTER> -->
</body>
</html>
