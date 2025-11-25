<?php
session_start(); // Start the session to retrieve session data

// Check if session data exists
if (!isset($_SESSION['worker_id']) || !isset($_SESSION['start_date']) || !isset($_SESSION['end_date']) || !isset($_SESSION['pickup_location'])) {
    die("<p>Session data is missing. Please go back and select a worker and fill in the necessary details.</p>");
}

// Retrieve session data
$worker_id = $_SESSION['worker_id'];
$start_date = $_SESSION['start_date'];
$end_date = $_SESSION['end_date'];
$pickup_location = $_SESSION['pickup_location'];

// Retrieve additional worker data from the database
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


/* Order Summary */
.order-summary {
    width: 45%; /* Adjust width for smaller summary */
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #e6ffe6; /* Light green background */
    margin-left: 500px;
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
    margin-left:850px;
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
    margin-top :200px;
    
    
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

<?php include("../header.php") ?>


    
     <!-- Order Summary -->
     <!-- Order Summary -->
<div class="order-summary">
    <h2>Order Summary</h2>
    <div id="summary">
        <div class="summary-item">
            <span>Baby sitting category:</span>
            <span id="summaryPetType"><?php echo $_SESSION['pet_type']; ?></span> <!-- Assuming this was stored in the session -->
        </div>
        <div class="summary-item">
            <span>Place Price:</span>
            <span id="summaryPlacePrice">৳<?php echo $_SESSION['price']; ?></span> <!-- Assuming this was stored in the session -->
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
            <span id="summaryDays">0</span> <!-- This will be calculated by JavaScript -->
        </div>
        <div class="summary-item">
            <span>Total Cost:</span>
            <span id="summaryCost">৳0</span> <!-- This will be calculated by JavaScript -->
        </div>
        <div class="summary-item">
            <span>Pickup Location:</span>
            <span id="summaryLocation"><?php echo $pickup_location; ?></span>
        </div>
        <div class="summary-item">
            <span>Start Date:</span>
            <span id="summaryStartDate"><?php echo $start_date; ?></span>
        </div>
        <div class="summary-item">
            <span>End Date:</span>
            <span id="summaryEndDate"><?php echo $end_date; ?></span>
        </div>
    </div>
</div>

<!-- Order Button -->
<form method="POST" action="placeOrder.php">
    <input type="hidden" name="worker_id" value="<?php echo $worker_id; ?>">
    <input type="hidden" name="worker_name" value="<?php echo $worker_name; ?>">
    <input type="hidden" name="worker_price" value="<?php echo $worker_price_per_day; ?>">
    <input type="hidden" name="pet_type" value="<?php echo $_SESSION['pet_type']; ?>">
    <input type="hidden" name="place_price" value="<?php echo $_SESSION['price']; ?>">
    <input type="hidden" name="days" value="" id="daysInput"> <!-- To be filled by JavaScript -->
    <input type="hidden" name="total_cost" value="" id="totalCostInput"> <!-- To be filled by JavaScript -->
    <button type="submit" >Confirm Order</button>
</form>

<script>
    // Fetch the start and end date
    const startDate = new Date("<?php echo $start_date; ?>");
    const endDate = new Date("<?php echo $end_date; ?>");
    const workerPricePerDay = <?php echo $worker_price_per_day; ?>;
    const placePrice = <?php echo $_SESSION['price']; ?>;

    // Calculate total days
    const diffTime = Math.abs(endDate - startDate)+1;
    const bookingDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); // Convert to days

    // Calculate total cost
    const totalCost = (workerPricePerDay + placePrice) * bookingDays;

    // Update the summary and hidden form inputs
    document.getElementById("summaryDays").textContent = bookingDays;
    document.getElementById("summaryCost").textContent = "৳" + totalCost;
    document.getElementById("daysInput").value = bookingDays;
    document.getElementById("totalCostInput").value = totalCost;
</script>





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


</body>
</html>
