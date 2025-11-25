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
    WHERE work_type = 3
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
        #apply {
    background-color: white;
    border: 2px solid #099468;
    color: #099468; 
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px; 
    cursor: pointer; 
}

#apply:hover {
    background-color: green;
    color: white; 
    border: 2px solid darkgreen; 
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
                    <button type="submit" id = "apply" >Apply</button>
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
                            <p><strong>Price:</strong> \৳{$row['price']}</p>
                            <p class='detail'>{$row['detail']}</p>
                            <form action='Petorder.php' method='get'>
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
