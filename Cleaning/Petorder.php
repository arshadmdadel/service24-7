<?php
session_start();

if (isset($_GET['worker_id'])) {
    $worker_id = intval($_GET['worker_id']); 

    if (!isset($_SESSION['pet_type']) || !isset($_SESSION['price'])) {
        die("<p>Session data missing. Please select a pet service first.</p>");
    }

    $pet_type = $_SESSION['pet_type'];
    $place_price = $_SESSION['price'];

    // Database connection
    $host = 'localhost'; // Change to your DB host
    $username = 'root';  // Change to your DB username
    $password = '';      // Change to your DB password
    $database = 'service24/7'; // Change to your DB name

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve worker details
    $sql = "SELECT * FROM worker WHERE id = $worker_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $worker = $result->fetch_assoc();
        $worker_name = $worker['fullname'];
        $worker_price_per_day = $worker['price'];
    } else {
        die("<p>Worker not found. Please go back and select a worker.</p>");
    }

    $conn->close();
} else {
    die("<p>No worker selected. Please go back and select a worker.</p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <!-- <link rel="stylesheet" href="petorder_style.css"> -->
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



 /* Page Layout */
/* Page Layout */
.order-container {
    display: flex;
    justify-content: space-between;
    align-items: center; /* Center vertically */
    flex-wrap: wrap; /* Adjust for smaller screens */
    width: 80%; /* Smaller width */
    margin: 40px auto; /* Center horizontally and add spacing at the top */
    padding: 20px;
    background-color: #e6ffe6; /* Light green background */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Subtle shadow */
}

/* Order Form */
.order-form {
    width: 45%; /* Adjust width for smaller form */
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    background-color: #fff; /* White background for contrast */
}

.order-form h2 {
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

.order-form label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
    color: #555;
}

.order-form input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); /* Subtle inner shadow */
}

.order-form input:focus {
    border-color: #4CAF50; /* Highlight on focus */
    outline: none;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.5); /* Green glow on focus */
}

.order-form button {
    width: 100%;
    padding: 12px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.order-form button:hover {
    background-color: #45a049;
}

/* Order Summary */
.order-summary {
    width: 45%; /* Adjust width for smaller summary */
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    background-color: #f9f9f9;
}

.order-summary h2 {
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

.summary-item {
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    font-size: 14px;
}

.summary-item span {
    font-weight: bold;
}

button {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: #111D15;
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
    margin-top :120px;
    
    
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
    position: relative; /* Necessary for pseudo-element positioning */
    color: #fff;
    padding: 5px 0;
}



#fhr {
    border: 1px solid white; /* White border */
    width: 95%;
    margin-left: 45px;
}

/* <!-- <FOOTER> --> */
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
            <select
                style="border: none; font-size: 20px; background-color: #fff; color: #099468; font-family: 'Times New Roman', Times, serif;"
                name="" id="">
                <option class="Service" value="" selected disabled>Service</option>
                <option class="Service" value="">Cleaning</option>
                <option class="Service" value="">Catering</option>
                <option class="Service" value="">Electrician</option>
                <option class="Service" value="">Security</option>
                <option class="Service" value="">Baby Sitting</option>
                <option class="Service" value="">Pet Caring</option>
            </select>
        </div>
        <div style="display: flex; align-items: center; justify-content: center;">
            <div><img src="../image/notification.png" alt=""></div>
            <div style="padding-left: 35px;">
                <a href="login.html">
                    <img src="../image/profile.png" alt="Profile">
                </a>
            </div>


        </div>
    </header>

    <!-- <HEADER> -->


    
    <div class="order-container">
    <!-- Input Form -->
    <div class="order-form">
        <h2>Order Details</h2>
        <form method="post" action="placeOrder.php" id="orderForm">

        <label for="pickupLocation">Pickup Location</label>
            <input type="text" name="pickup_location" id="pickupLocation" placeholder="Enter location" onchange="updateSummary()" required>
            
            <label for="startDate">Start Date</label>
            <input type="date" name="start_date" id="startDate" onchange="updateSummary()" required>

            <label for="endDate">End Date</label>
            <input type="date" name="end_date" id="endDate" onchange="updateSummary()" required>

            <input type="hidden" name="worker_id" value="<?php echo $worker_id; ?>"> 
            <input type="hidden" name="pet_type" value="<?php echo $pet_type; ?>"> 
            <input type="hidden" name="place_price" value="<?php echo $place_price; ?>"> 
            <input type="hidden" name="worker_price" value="<?php echo $worker_price_per_day; ?>"> 

            <button type="submit">Place Order</button>
        </form>
    </div>

    <!-- Order Summary -->
    <div class="order-summary">
        <h2>Order Summary</h2>
        <div id="summary">
            <div class="summary-item">
                <span>Pet Type:</span>
                <span id="summaryPetType"><?php echo $pet_type; ?></span>
            </div>
            <div class="summary-item">
                <span>Place Price:</span>
                <span id="summaryPlacePrice">৳<?php echo $place_price; ?></span>
            </div>
            <div class="summary-item">
                <span>Worker Name:</span>
                <span id="summaryWorkerName"><?php echo $worker_name; ?></span>
            </div>
            <div class="summary-item">
                <span>Worker Price Per Day:</span>
                <span id="summaryWorkerPrice">৳<?php echo $worker_price_per_day; ?></span>
            </div>
            <div class="summary-item">
                <span>Booking Days:</span>
                <span id="summaryDays">-</span>
            </div>
            <div class="summary-item">
                <span>Total Cost:</span>
                <span id="summaryCost">-</span>
            </div>
            <div class="summary-item">
                <span>Pickup Location:</span>
                <span id="summaryLocation">-</span>
            </div>
        </div>
        <!-- <button onclick="placeOrder()">Place Order</button> -->
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
                <a class="flink">Services</a>
                <a class="flink">Our Team</a>
            </div>
            <div id="foot3">
                <h3 class="flink">Know More</h3>
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

<script>
    function calculateDays(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        return Math.ceil((end - start) / (1000 * 60 * 60 * 24)) || 0;
    }

    function calculateTotalCost(days, workerPrice, placePrice) {
        return days * workerPrice + placePrice;
    }

    function updateSummary() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const pickupLocation = document.getElementById('pickupLocation').value;

        const workerPrice = <?php echo $worker_price_per_day; ?>;
        const placePrice = <?php echo $place_price; ?>;

        const days = calculateDays(startDate, endDate);
        const totalCost = calculateTotalCost(days, workerPrice, placePrice);

        document.getElementById('summaryDays').innerText = days > 0 ? days : '-';
        document.getElementById('summaryCost').innerText = days > 0 ? `৳${totalCost}` : '-';
        document.getElementById('summaryLocation').innerText = pickupLocation || '-';
    }

    function placeOrder() {

    }
</script>
</body>
</html>
