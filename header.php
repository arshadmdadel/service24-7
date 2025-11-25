<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$userID = $_SESSION['user_id'];
$notifications = [];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'service24/7');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch unread notifications for the user
$stmt = $conn->prepare("
    SELECT o.id, o.service_name, o.time, w.fullName AS worker_name, o.notification_status, o.accept_status 
    FROM `order` o 
    JOIN `worker` w ON o.worker_id = w.id 
    WHERE o.user_id = ? AND o.state = 1
    ORDER BY o.time DESC
");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        *{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    overflow-x: hidden;
}
/* <!-- <HEADER> --> */
header {
    height: 10vh;
    width: 100vw;
    background-color: white;
    display: flex;
    justify-content: space-evenly;
    align-items: center;
}

.A {
    text-decoration: none;
    position: relative; 
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
}

a:hover::after {
    width: 100%; 
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
    border-bottom: 2px solid #099468;
/* } */ 

select {
    outline: none; /* Removes the default outline for better visuals */
}


/* <!-- <HEADER> --> */
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
            <a href="/service24_today/home_page.php" class="A">Home</a>
            <a href="/service24_today/Review\review.html" class="A">Review</a>
            <a href="/service24_today/user_order_page/worker_homepage.php" class="A">Order</a>
            <a href="" class="A">Contact</a>
            <select style="border: none; font-size: 20px; background-color: #fff; color: #099468; font-family: 'Times New Roman', Times, serif;"
            name="service" id="serviceSelect" onchange="navigateToPage(this.value)">
            <option class="Service" value="" selected disabled>Service</option>
            <option class="Service" value="/service24_today/Cleaning/cleaning.html">Cleaning</option>
            <option class="Service" value= "/service24_today/catering\catering.php">Catering</option>
            <option class="Service" value="/service24_today/Electrician/electrician.html">Electrician</option>
            <option class="Service" value="/service24_today/Security\index.html">Security</option>
            <option class="Service" value="/service24_today/Baby sitting/baby.html">Baby Sitting</option>
            <option class="Service" value="/service24_today/petcaring/pet1.html">Pet Caring</option>
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
    <div id="notification-container" style="position: relative;">
        <!-- Notification Icon -->
        <img src="/service24_today/notification.png" alt="Notifications" onclick="openNotificationModal()" 
             style=" cursor: pointer; position: relative;">
        <?php if (count($notifications) > 0): ?>
            <!-- Notification Count -->
            <span id="notification-count" style="position: absolute; top: 0; right: 0; background: red; color: white; font-size: 12px; border-radius: 50%; padding: 2px 6px;">
                <?php echo count($notifications); ?>
            </span>
        <?php endif; ?>

        <!-- Notification Modal -->
        <div id="notification-modal" style="display: none; position: fixed; top: 20%; left: 80%; transform: translate(-50%, -50%); width: 400px; background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); z-index: 1000; padding: 20px;">
            <h4 style="margin-bottom: 15px; font-size: 18px; color: #333;">Notifications</h4>
            <ul style="list-style: none; padding: 0; max-height: 300px; overflow-y: auto; margin: 0;">
                <?php if (count($notifications) > 0): ?>
                    <?php foreach ($notifications as $notification): ?>
                        <li style="border-bottom: 1px solid #f0f0f0; padding: 10px;">
                            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($notification['id']); ?></p>
                            <p><strong>Service:</strong> 
    <?php 
    $serviceName = $notification['service_name'];

    // Logic to map specific service names
    switch (strtolower($serviceName)) { // Convert to lowercase for case-insensitive comparison
        case "0":
            echo "Pet Caring";
            break;
        case "1":
            echo "Electrician";
            break;
        case "2":
            echo "Catering";
            break;
        case "3":
            echo "Cleaning";
            break;
        case "4":
            echo "Baby Sitting";
            break;
        default:
            // Fallback to displaying the original service name if no match is found
            echo htmlspecialchars($serviceName);
            break;
    }
    ?>
</p>

                            <p><strong>Status:</strong> 
                                <?php
                                if ($notification['notification_status'] == 0) {
                                    echo "Pending";
                                } elseif ($notification['accept_status'] == 1) {
                                    echo "Accepted";
                                } elseif ($notification['accept_status'] == 2) {
                                    echo "Completed";
                                } else {
                                    echo "Declined";
                                }
                                ?>
                            </p>
                            <p><strong>order Time:</strong> <?php echo htmlspecialchars($notification['time']); ?></p>
                        </li>
                    <?php endforeach; ?>
                    <!-- <button onclick="markAllAsRead()" style="margin-top: 15px; padding: 8px 12px; background: #099468; color: white; border: none; border-radius: 3px; cursor: pointer;">
                        Mark All as Read
                    </button> -->
                <?php else: ?>
                    <li style="text-align: center; padding: 10px;">No new notifications.</li>
                <?php endif; ?>
            </ul>
            <button onclick="closeNotificationModal()" style="margin-top: 10px; padding: 8px 12px; background: #d9534f; color: white; border: none; border-radius: 3px; cursor: pointer;">
                Close
            </button>
        </div>
    </div>
    <div style="padding-left: 35px;">
                <a href="login.html">
                    <img src="/service24_today/profile.png" alt="Profile">
                </a>
            </div>
</div>


    </header>
    <script>
function openNotificationModal() {
    const modal = document.getElementById('notification-modal');
    modal.style.display = 'block';
}

function closeNotificationModal() {
    const modal = document.getElementById('notification-modal');
    modal.style.display = 'none';
    fetch('update_state.php', {
        method: 'POST',
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // alert('Notifications closed successfully!');
            // Additional actions like hiding the modal
            window.location.href = 'home_page.php';
        } else {
            alert('Failed to update state. Please try again.');
        }
    })
    .catch(error => console.error('Error:', error));
}

// function markAllAsRead() {
//     // Hide the notification count
//     const notificationCount = document.getElementById('notification-count');
//     if (notificationCount) {
//         notificationCount.style.display = 'none';
//     }

//     // Clear the notifications in the modal
//     const modalContent = document.getElementById('notification-modal');
//     modalContent.innerHTML = '<h4 style="margin-bottom: 15px; font-size: 18px; color: #333;">Notifications</h4><ul style="list-style: none; padding: 0; margin: 0;"><li style="text-align: center; padding: 10px;">No new notifications.</li></ul><button onclick="closeNotificationModal()" style="margin-top: 10px; padding: 8px 12px; background: #d9534f; color: white; border: none; border-radius: 3px; cursor: pointer;">Close</button>';

//     // Make an AJAX call to `clearnotification.php`
//     fetch('clearnotification.php')
//         .then(response => response.json())
//         .then(data => {
//             console.log('Notifications cleared:', data.message);
//         })
//         .catch(error => {
//             console.error('Error clearing notifications:', error);
//         });
// }

</script>

    <!-- <HEADER> -->

</body>
</html>

