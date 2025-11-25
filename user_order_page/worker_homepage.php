<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user'])) {
    header("Location: user_login.php");
    exit();
}

$userID = $_SESSION['user_id'];
$orders = [];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'service24/7');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define the mapping for work_type to service_name
$workTypeMapping = [
    0 => 'Pet Caring',
    1 => 'Electrician',
    2 => 'Catering',
    3 => 'Cleaning',
    4 => 'Baby Sitting',
    5 => 'Security'
];

// Fetch all orders for the logged-in user, including worker details
$stmt = $conn->prepare("
SELECT o.id, o.worker_id, o.service_name, o.time, o.accept_status, o.notification_status, 
       w.fullname AS worker_name, w.work_type, w.price AS worker_price, w.rating
FROM `order` o
JOIN worker w ON o.worker_id = w.id
WHERE o.user_id = ?
ORDER BY o.id DESC;

");
$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Translate work_type to service_name
    $serviceName = $workTypeMapping[$row['work_type']] ?? 'Unknown Service';

    // Add the order to the array with the translated service name
    $orders[] = [
        'id' => $row['id'],
        'worker_id' => $row['worker_id'],
        'service_name' => $serviceName,
        'worker_name' => $row['worker_name'],
        'time' => $row['time'],
        'accept_status' => $row['accept_status'],
        'notification_status' => $row['notification_status'],
        'worker_price' => $row['worker_price'],
        'worker_rating' => $row['rating']
    ];
}


$stmt = $conn->prepare("
    select * 
    from messages
    where user_id = ?
");
$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();

$msg = [];

while ($row = $result->fetch_assoc()) {

    // Add the order to the array with the translated service name
    // echo $row['content'];
    // echo "<br>";
    $msg[] = [
        'id' => $row['id'],
        'user_id' => $row['user_id'],
        'worker_id' => $row['worker_id'],
        'content' => $row['content'],
        'flag' => $row['flag'],
        'order_id' => $row['order_id']
    ];
}





$stmt->close();
$conn->close();




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Orders</title>

    <style>
        /* <!-- <HEADER> --> */
        * {
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
            position: relative;
            /* Necessary for pseudo-element positioning */
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
            transition: width 0.3s ease;
            /* Smooth animation */
        }

        a:hover::after {
            width: 100%;
            /* Expands the underline */
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
            outline: none;
            /* Removes the default outline for better visuals */
        }


        /* <!-- <HEADER> --> */


        header {
            background: var(--primary-color);
            color: var(--white);
            /* padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center; */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .container {
            min-height: 60vh;
            padding: 20px;
            max-width: 800px;
            margin: auto;
            
        }

        .order-box {
            background: var(--white);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .order-box:hover {
        transform: scale(1.05);
        }

        .order-details p {
            margin: 5px 0;
            font-size: 0.9rem;
        }

        button {
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .review-btn {
            background: var(--primary-color);
            color: var(--white);
            transition: background var(--transition);
        }

        .review-btn:hover {
            background: #388E3C;
        }

        /* <!-- <FOOTER> --> */

        footer {
            height: 30vh;
            width: 100vw;
            background-color: #111D15;
            display: grid;
            align-items: center;
            justify-content: center;
            color: white;


        }

        #content {
            height: 20vh;
            width: 90vw;
            margin-left: 150px;
            display: flex;
            align-items: center;
            justify-content: space-evenly;
            color: white;
        }

        #foot1 {
            height: 15vh;
            width: 20vw;
        }

        #foot2 {
            height: 15vh;
            width: 10vw;
            display: grid;
            align-items: center;
            justify-content: center;
            color: white;
        }

        #foot3 {
            height: 15vh;
            width: 15vw;
            display: grid;
            align-items: center;
            justify-content: center;
            color: white;
        }

        #foot5 {
            width: 100vw;
            text-align: center;
        }

        .flink {
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


        /* Rifat style code start */
        .messageButton {
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #099468;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            height: 5vh;
            width: 6.5vw;
        }

        .messenger {
            /* display: none; */
            /* position: fixed; */
            /* bottom: 20px;
            right: 20px; */
            width: 400px;
            height: 500px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            z-index: 1000;
        }

        .messengerHeader {
            padding: 10px;
            background-color: #099468;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .messengerHeader span {
            font-size: 18px;
        }

        .closeMessenger {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }

        .messengerBody {
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            background-color: #f9f9f9;
        }

        .message {
            margin: 5px 0;
            padding: 10px;
            border-radius: 10px;
            max-width: 80%;
            word-wrap: break-word;
        }

        .user-message {
            background-color: #099468;
            color: white;
            align-self: flex-end;
            margin-left: auto;
        }

        .incoming-message {
            background-color: #e1e1e1;
            color: #333;
            align-self: flex-start;
            margin-right: auto;
        }

        #chat-box {
            position: relative;
        }

        .messengerFooter {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ccc;
            background-color: white;
            position: absolute;
            bottom: 0px;
            background-color: #111D15;
            width: 100%;
            height: 7vh;
        }

        .messageInput {
            flex: 1;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .sendMessage {
            padding: 5px 10px;
            margin-left: 5px;
            background-color: #099468;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;

        }




        /* Rifat style code end */


        /* <!-- <FOOTER> --> */


        .star-rating {
    display: inline-block;
    font-size: 24px; /* Adjust size of stars */
    color: #FFD700; /* Gold color for filled stars */
}

.star.filled {
    color: #FFD700; /* Gold */
}

.star.empty {
    color: #ccc; /* Light gray for empty stars */
}
/* General button styling for all buttons */
button {
    border: 2px solid #3CB371; /* Light green border */
    background-color: white; /* White background */
    color: #4CAF50; /* Green text color */
    font-size: 16px; /* Adjust font size */
    padding: 10px 20px; /* Add padding for comfort */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Pointer cursor on hover */
    transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effect */
}

/* Hover effect for all buttons */
button:hover {
    background-color: #90EE90; /* Light green background on hover */
    color: white; /* White text on hover */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow for hover effect */
}

    </style>

</head>

<body>
    <!-- HEADER -->
    <?php include("../header.php") ?> 

    <!-- Orders Section -->
    <div class="container">
        <h2 style="margin-left:350px; margin-bottom:50px;">Your Orders</h2>
        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-box">
                    <div class="order-details">

                        <p><strong>Order ID:</strong> <span id="deep_id<?php echo $order['worker_id'] ?><?php echo $order['id'] ?>"><?php echo htmlspecialchars($order['id']); ?></span> </p>
                        <p><strong>Service:</strong> <?php echo htmlspecialchars($order['service_name']); ?></p>
                        <p><strong>Worker:</strong> <?php echo htmlspecialchars($order['worker_name']); ?></p>
                        <p><strong>Time:</strong> <?php echo htmlspecialchars($order['time']); ?></p>
                        <p><strong>Status:</strong>
                            <?php
                            if ($order['notification_status'] == 0) {
                                echo "Pending";
                            } elseif ($order['accept_status'] == 1) {
                                echo "Accepted";
                            } elseif ($order['accept_status'] == 0) {
                                echo "Declined";
                            } elseif ($order['accept_status'] == 2) {
                                echo "Completed";
                            }
                            ?>
                        </p>
                        <!-- <?php if ($order['accept_status'] == 1) { ?>

                            <button class="messageButton" id="messageButton<?php echo $order['worker_id'] ?><?php echo $order['id']; ?>" onclick="open_messenger(<?php echo $order['worker_id'] ?>, <?php echo $order['id'] ?> )">Message</button>

                        <?php } ?> -->

                        <!-- <p><strong>Worker Price:</strong> $<?php echo htmlspecialchars($order['worker_price']); ?></p> -->
                        <p><strong>Rating:</strong> <?php echo htmlspecialchars($order['worker_rating']); ?> / 5</p>
                        <form method="POST" action="order_details.php">
                            <input type="hidden" name="orderID" value="<?php echo htmlspecialchars($order['id']); ?>">
                            <input type="hidden" name="service_name" value="<?php echo htmlspecialchars($order['service_name']); ?>">
                            <input type="hidden" name="accept_status" value="<?php echo htmlspecialchars($order['accept_status']); ?>">
                            <button class="review-btn" type="submit">view details</button>
                        </form>
                        <br>

                        <?php if ($order['accept_status'] == 2): ?>


                            <?php

                            // Database connection
$conn = new mysqli('localhost', 'root', '', 'service24/7');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the order already has a rating
$sql_check_rating = "SELECT rating, comment FROM rating WHERE order_id = ?";
if ($stmt = $conn->prepare($sql_check_rating)) {
    $stmt->bind_param("i", $order['id']);
    $stmt->execute();
    $stmt->bind_result($rating, $review_text);

    if ($stmt->fetch()) {
        // If a review exists, display the review details with stars
        echo '<div class="review-info">';
        echo '<h4>Your Rating:</h4>';
        echo '<div class="star-rating">';
        
        // Display filled and empty stars based on the rating
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                echo '<span class="star filled">★</span>'; // Filled star
            } else {
                echo '<span class="star empty">☆</span>'; // Empty star
            }
        }
        
        echo '</div>'; // Close star-rating div
        echo '<h4><span>Your Review:</span> ' . htmlspecialchars($review_text) . '</h4>';
        echo '</div>';
    } else {
        // If no review exists, show the "Leave Review" button
        ?>
        <form method="POST" action="../rating/rating.php">
            <input type="hidden" name="orderID" value="<?php echo htmlspecialchars($order['id']); ?>">
            <button class="review-btn" type="submit">Leave Review</button>
        </form>
        <?php
    }
    $stmt->close();
}

?>


                        <?php endif; ?>
                        <?php if ($order['accept_status'] == 1) { ?>

<button class="messageButton" id="messageButton<?php echo $order['worker_id'] ?><?php echo $order['id']; ?>" onclick="open_messenger(<?php echo $order['worker_id'] ?>, <?php echo $order['id'] ?> )">Message</button>

<?php } ?>
                    </div>

                    <?php if ($order['accept_status'] == 1) { ?>

                        <div id="chat-box">
                            <div class="messenger" id="messenger<?php echo $order['worker_id'] ?><?php echo $order['id'] ?>">
                                <div class="messengerHeader" id="messengerHeader<?php echo $order['worker_id'] ?><?php echo $order['id'] ?>">
                                    <span><?php echo $order['worker_name'] ?></span>
                                    <button class="closeMessenger" id="closeMessenger<?php echo $order['worker_id'] ?><?php echo $order['id'] ?> " onclick="close_messenger(<?php echo $order['worker_id'] ?>, <?php echo $order['id'] ?>)">&times;</button>
                                </div>
                                <div class="messengerBody" id="messengerBody<?php echo $order['worker_id'] ?><?php echo $order['id'] ?>">

                                    <?php foreach ($msg as $m) { ?>
                                        <?php $val = $m['worker_id'] ?? 0; ?>
                                        <?php $val2 = $order['worker_id'] ?? 0; ?>
                                        <?php $flag = $m['flag'] ?? 0; ?>

                                        <?php $val3 = $m['order_id'] ?? 0; ?>
                                        <?php $val4 = $order['id'] ?? 0; ?>

                                        <?php if ($val2 == $val && $val3==$val4) { ?>
                                            <?php if ($flag == 1) { ?>
                                                <div class="message user-message"> <?php echo $m['content'] ?> </div>
                                            <?php } else { ?>
                                                <div class="message incoming-message"> <?php echo $m['content'] ?> </div>
                                            <?php    } ?>

                                        <?php  } ?>
                                    <?php } ?>


                                </div>
                                <div class="messengerFooter" id="messengerFooter<?php echo $order['worker_id'] ?><?php echo $order['id'] ?>">
                                    <input type="text" class="messageInput" id="messageInput<?php echo $order['worker_id'] ?><?php echo $order['id'] ?>" placeholder="Type your message...">
                                    <button class="sendMessage" id="sendMessage<?php echo $order['worker_id'] ?><?php echo $order['id'] ?>" onclick="send_message(<?php echo $order['worker_id'] ?>, <?php echo $order['id'] ?>)">Send</button>
                                </div>
                            </div>
                        </div>

                    <?php   } ?>


                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </div>



    <?php include("../footer.php") ?>



    <script>
        // Rifat script start
        let worker_id;
        let messageButton;
        let messenger;
        let closeMessenger;
        let messageInput;
        let sendMessage;
        let messengerBody;
        // messageButton = document.getElementById("messageButton" + worker_id);
        // messenger = document.getElementById("messenger" + worker_id);
        // closeMessenger = document.getElementById("closeMessenger" + worker_id);
        // messageInput = document.getElementById("messageInput" + worker_id);
        // sendMessage = document.getElementById("sendMessage" + worker_id);
        // messengerBody = document.getElementById("messengerBody" + worker_id);

        let deep_id;


        function open_messenger(id, order_id) {
            worker_id = id;
            // order_id = order_id;
            messenger = document.getElementById("messenger" + worker_id + order_id);
            messenger.style.display = "block";
            messageInput = document.getElementById("messageInput" + worker_id + order_id);
            sendMessage = document.getElementById("sendMessage" + worker_id + order_id);
            messageInput = document.getElementById("messageInput" + worker_id + order_id);
            messengerBody = document.getElementById("messengerBody" + worker_id + order_id);
            const message = messageInput.value.trim();

            deep_id = document.getElementById("deep_id" + worker_id + order_id).innerText;
            console.log(deep_id);
            fetch_msg(message, worker_id, messageInput, messengerBody, deep_id);
        }

        function close_messenger(id, order_id) {
            worker_id = id;
            // order_id = order_id;
            messenger = document.getElementById("messenger" + worker_id + order_id);
            messenger.style.display = "none";
        }


        function send_message(id, order_id) {
            worker_id = id;
            // order_id = order_id;
            sendMessage = document.getElementById("sendMessage" + worker_id + order_id);
            messageInput = document.getElementById("messageInput" + worker_id + order_id);
            messengerBody = document.getElementById("messengerBody" + worker_id + order_id);
            const message = messageInput.value.trim();
            deep_id = document.getElementById("deep_id" + worker_id + order_id).innerText;
            console.log(deep_id);

            if (message) {
                addMessage(message, "user-message", messengerBody);

                fetch("save_message.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: new URLSearchParams({
                            message: message,
                            sender: id, // You can modify this as needed
                            flag: 1,
                            order_id: deep_id,
                        }),
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.status === "success") {
                            console.log("Message saved:", data.message);
                        } else {
                            console.error("Error saving message:", data.message);
                        }
                    })
                    .catch((error) => console.error("Error:", error));
                messageInput.value = ""; // Clear the input
                messengerBody.scrollTop = messengerBody.scrollHeight; // Scroll to the bottom

                setTimeout(() => {
                    addMessage("This is a reply.", "incoming-message", messengerBody);
                }, 1000);

                location.reload(); // Refreshes the entire page

            }


        }


        
        function fetch_msg(message, id, messengerInput, messengerBody, deep_id) {
            setInterval(() => {
                fetch("save_message.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: new URLSearchParams({
                            message: message,
                            sender: id, // You can modify this as needed
                            flag: 1,
                            order_id: deep_id,
                        }),
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.status === "success") {
                            console.log("Message saved:", data.message);
                        } else {
                            console.error("Error saving message:", data.message);
                        }
                    })
                    .catch((error) => console.error("Error:", error));
                // messageInput.value = ""; // Clear the input
                // messengerBody.scrollTop = messengerBody.scrollHeight; // Scroll to the bottom
                location.reload(); // Refreshes the entire page

            }, 1000);
        }

        function addMessage(text, className, messengerBody) {
            const messageDiv = document.createElement("div");
            messageDiv.textContent = text;
            messageDiv.classList.add("message", className);
            messengerBody.appendChild(messageDiv);

        }



        // Rifat script end
    </script>
</body>

</html>