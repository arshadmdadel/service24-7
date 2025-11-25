<?php
session_start();

// Ensure the worker is logged in
if (!isset($_SESSION['worker_id']) || !isset($_SESSION['worker_name'])) {
    header("Location: worker_login.php");
    exit();
}

$workerID = $_SESSION['worker_id'];
$notifications = [];
$acceptedOrders = [];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'service24/7');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// // Handle accept/decline actions
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $orderID = $_POST['orderID'] ?? '';
//     $action = $_POST['action'] ?? '';


//     if (!empty($orderID)) {
//         if ($action === 'accept' || $action === 'decline') {
//             $acceptStatus = ($action === 'accept') ? 1 : 0;
//             $stmt = $conn->prepare("UPDATE `order` SET accept_status = ?, notification_status = 1 , state = 1 WHERE id = ? AND worker_id = ?");
//             $stmt->bind_param("isi", $acceptStatus, $orderID, $workerID);
//             $stmt->execute();
//             $stmt->close();
//         } elseif ($action === 'done') {
//             $stmt = $conn->prepare("UPDATE `order` SET accept_status = 2 , state = 1 WHERE id = ? AND worker_id = ?");
//             $stmt->bind_param("is", $orderID, $workerID);
//             $stmt->execute();
//             $stmt->close();
//         }
//     }
// }

// Fetch notifications
$stmt = $conn->prepare("
    SELECT o.id, o.service_name, o.time, u.fullName AS username 
    FROM `order` o 
    JOIN `user` u ON o.user_id = u.id 
    WHERE o.worker_id = ? AND o.notification_status = 0
");
$stmt->bind_param("s", $workerID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();

// Fetch accepted orders
$stmt = $conn->prepare("
    SELECT o.id, o.service_name, o.time, u.id as user_id, u.fullName AS username 
    FROM `order` o 
    JOIN `user` u ON o.user_id = u.id 
    WHERE o.worker_id = ? AND o.accept_status = 1
");
$stmt->bind_param("s", $workerID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $acceptedOrders[] = $row;
}

$stmt = $conn->prepare("
    select * 
    from messages
    where worker_id = ?
");
$stmt->bind_param("s", $workerID);
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
    <title>Worker Dashboard</title>
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #81C784;
            --text-color: #333;
            --bg-color: #f7f7f7;
            --white: #fff;
            --danger-color: #F44336;
            --transition: 0.3s ease;
        }

        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
        }

        header {
            background: var(--primary-color);
            color: var(--white);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .notification-btn {
            background: var(--secondary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background var(--transition);
        }

        .notification-btn:hover {
            background: var(--primary-color);
        }

        .container {
            padding: 20px;
            max-width: 800px;
            margin: auto;
        }

        .order-box,
        .notification-popup {
            background: var(--white);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .order-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .accept-btn {
            background: var(--primary-color);
            color: var(--white);
            transition: background var(--transition);
        }

        .accept-btn:hover {
            background: #388E3C;
        }

        .decline-btn,
        .close-btn {
            background: var(--danger-color);
            color: var(--white);
            transition: background var(--transition);
        }

        .decline-btn:hover,
        .close-btn:hover {
            background: #D32F2F;
        }

        .done-btn {
            background: var(--primary-color);
            color: var(--white);
            transition: background var(--transition);
        }

        .done-btn:hover {
            background: #388E3C;
        }

        .notification-popup {
            position: absolute;
            margin-top: 30px;
            top: 60px;
            /* Will be updated dynamically */
            left: 80vw;
            /* Will be updated dynamically */
            max-width: 400px;
            background: var(--white);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 10;
        }

        .notification-popup h2 {
            margin-top: 0;
            font-size: 1.2rem;
        }

        .notification-popup ul {
            list-style: none;
            padding: 0;
        }

        .notification-popup li {
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .notification-popup .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--danger-color);
            color: var(--white);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 1rem;
            transition: background var(--transition);
        }

        .notification-popup .close-btn:hover {
            background: #D32F2F;
        }






        .messageButton {
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #099468;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            height: 7vh;
            width: 7vw;
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
    </style>
</head>

<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['worker_name']); ?>!</h1>
        <button class="notification-btn" onclick="togglePopup()">Notifications</button>
    </header>

    <div class="container">
        <h2>Your Dashboard</h2>
        <h3>Accepted Orders</h3>
        <?php if (count($acceptedOrders) > 0): ?>
            <?php foreach ($acceptedOrders as $order): ?>
                <div class="order-box">
                    <div class="order-details">
                        <p><strong>Order ID:</strong> <span id="deep_id<?php echo $order['user_id']; ?><?php echo $order['id']; ?>"><?php echo htmlspecialchars($order['id']); ?></span> </p>
                        <p><strong>Service:</strong> <?php echo htmlspecialchars($order['service_name']); ?></p>
                        <p><strong>User Name:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                        <p><strong>User id:</strong> <?php echo htmlspecialchars($order['user_id']); ?></p>
                        <p><strong>Time:</strong> <?php echo htmlspecialchars($order['time']); ?></p>
                        <button class="messageButton" id="messageButton<?php echo $order['user_id'] ?><?php echo $order['id']; ?>" onclick="open_messenger(<?php echo $order['user_id'] ?>,<?php echo $order['id']; ?>)">Message</button>

                    </div>


                    <div id="chat-box">
                        <div class="messenger" id="messenger<?php echo $order['user_id'] ?><?php echo $order['id']; ?>">
                            <div class="messengerHeader" id="messengerHeader<?php echo $order['user_id'] ?><?php echo $order['id']; ?>">
                                <span><?php echo $order['username'] ?></span>
                                <button class="closeMessenger" id="closeMessenger<?php echo $order['user_id'] ?><?php echo $order['id']; ?> " onclick="close_messenger(<?php echo $order['user_id'] ?>,<?php echo $order['id']; ?>)">&times;</button>
                            </div>
                            <div class="messengerBody" id="messengerBody<?php echo $order['user_id'] ?><?php echo $order['id']; ?>">

                                <?php foreach ($msg as $m) { ?>
                                    <?php $val = $m['user_id'] ?? 0; ?>
                                    <?php $val2 = $order['user_id'] ?? 0; ?>
                                    <?php $flag = $m['flag'] ?? 0; ?>

                                    <?php $val3 = $m['order_id'] ?? 0; ?>
                                    <?php $val4 = $order['id'] ?? 0; ?>

                                    <?php if ($val2 == $val && $val3==$val4) { ?>
                                        <?php if ($flag == 0) { ?>
                                            <div class="message user-message"> <?php echo $m['content'] ?> </div>
                                        <?php } else { ?>
                                            <div class="message incoming-message"> <?php echo $m['content'] ?> </div>
                                        <?php    } ?>

                                    <?php  } ?>
                                <?php } ?>
                            </div>
                            <div class="messengerFooter" id="messengerFooter<?php echo $order['user_id'] ?><?php echo $order['id']; ?>">
                                <input type="text" class="messageInput" id="messageInput<?php echo $order['user_id'] ?><?php echo $order['id']; ?>" placeholder="Type your message...">
                                <button class="sendMessage" id="sendMessage<?php echo $order['user_id'] ?><?php echo $order['id']; ?>" onclick="send_message(<?php echo $order['user_id'] ?>,<?php echo $order['id']; ?>)">Send</button>
                            </div>
                        </div>
                    </div>


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

$stmt->bind_param('i', $order['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0): // If the payment record exists
?>
    <form method="POST" action="handle_data.php">
        <input type="hidden" name="orderID" value="<?php echo htmlspecialchars($order['id']); ?>">
        <button class="done-btn" type="submit" name="action" value="done">Done</button>
    </form>
<?php
else: // If no payment record exists
?>
    <span style="color: red;"></span>
<?php
endif;

$stmt->close();
?>

<!-- 
                    <form method="POST" action="handle_data.php">
                        <input type="hidden" name="orderID" value="<?php echo htmlspecialchars($order['id']); ?>">
                        <button class="done-btn" type="submit" name="action" value="done">Done</button>
                    </form> -->
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No accepted orders at the moment.</p>
        <?php endif; ?>
    </div>

    <div id="notificationPopup" class="notification-popup">
        <button class="close-btn" onclick="togglePopup()">Close</button>
        <h2>New Notifications</h2>
        <?php if (count($notifications) > 0): ?>
            <ul>
                <?php foreach ($notifications as $notification): ?>
                    <li>
                        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($notification['id']); ?></p>
                        <!-- <p><strong>Service:</strong> <?php echo htmlspecialchars($notification['service_name']); ?></p>
                        <p><strong>User Name:</strong> <?php echo htmlspecialchars($notification['username']); ?></p> -->
                        <p><strong>Time:</strong> <?php echo htmlspecialchars($notification['time']); ?></p>
                        <form method="POST" action="handle_data.php">
                            <input type="hidden" name="orderID" value="<?php echo htmlspecialchars($notification['id']); ?>">
                            <button type="submit" name="action" value="accept" class="accept-btn">Accept</button>
                            <button type="submit" name="action" value="decline" class="decline-btn">Decline</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No new notifications.</p>
        <?php endif; ?>
    </div>

    <script>
        function togglePopup() {
            const popup = document.getElementById('notificationPopup');
            const button = document.querySelector('.notification-btn');
            const rect = button.getBoundingClientRect();

            // Position the popup below the button
            popup.style.top = `${rect.bottom + window.scrollY}px`;
            popup.style.right = `${window.innerWidth - rect.right}px`;
            popup.style.display = popup.style.display === 'block' ? 'none' : 'block';
        }



        // Rifat script start
        let user_id;
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
            user_id = id;
            messenger = document.getElementById("messenger" + user_id + order_id);
            messenger.style.display = "block";
            messageInput = document.getElementById("messageInput" + user_id + order_id);
            sendMessage = document.getElementById("sendMessage" + user_id + order_id);
            messageInput = document.getElementById("messageInput" + user_id + order_id);
            messengerBody = document.getElementById("messengerBody" + user_id + order_id);
            const message = messageInput.value.trim();
            deep_id = document.getElementById("deep_id" + user_id + order_id).innerText;
            console.log(deep_id);
            fetch_msg(message, user_id, messageInput, messengerBody, deep_id);
        }

        function close_messenger(id, order_id) {
            user_id = id;
            messenger = document.getElementById("messenger" + user_id + order_id);
            messenger.style.display = "none";
        }


        function send_message(id, order_id) {
            user_id = id;
            sendMessage = document.getElementById("sendMessage" + user_id + order_id);
            messageInput = document.getElementById("messageInput" + user_id + order_id);
            messengerBody = document.getElementById("messengerBody" + user_id + order_id);
            const message = messageInput.value.trim();
            deep_id = document.getElementById("deep_id" + user_id + order_id).innerText;
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
                            sender: user_id, // You can modify this as needed
                            flag: 0,
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

                // setTimeout(() => {
                //     addMessage("This is a reply.", "incoming-message", messengerBody);
                // }, 1000);
                // window.location.replace('worker_homepage.php');
                location.reload(); // Refreshes the entire page

                // setTimeout(function() {
                //     location.reload(); // Refreshes the entire page
                // }, 2000); // 5 seconds



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
                            flag: 0,
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
    </script>
</body>

</html>