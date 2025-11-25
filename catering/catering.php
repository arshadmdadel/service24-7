<?php
session_start();

$items = [
    ['name' => 'Item1', 'price' => 5, 'image' => 'cater1.png'],
    ['name' => 'Item2', 'price' => 8, 'image' => 'cater2.png'],
    ['name' => 'Item3', 'price' => 10, 'image' => 'cater3.png'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedItems = [];
    foreach ($_POST['items'] as $key => $quantity) {
        if ($quantity > 0) {
            $selectedItems[] = [
                'item' => htmlspecialchars($_POST['item_names'][$key]),
                'quantity' => (int)$quantity,
            ];
        }
    }
    // Save selected items to the session
    $_SESSION['cart'] = $selectedItems;

    // Redirect to order.php
    header("Location: filter.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catering</title>
    <style>
        #order-button {
            height: 8vh;
            width: 15vw;
            color: white;
            border: none;
            font-size: 20px;
            font-weight: bold;
            border-radius: 10px;
            background-color: #099468;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 10px;
        }

        #order-button:hover {
            transform: scale(1.1);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
        }

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


        #cater1{
            height: 33vh;
            width: 100vw;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            background-color: #fdf5ea;;
        }

        .cater1_pic{
            height: 33vh;
            width: 30vw
        }

        .cater2_pic{
            height: 20vh;
            width: 15vw;
            border-radius: 15px;
            margin-top: 10px;
        }

        #item_block{

            width: 100vw;
            text-align: center;
            justify-content: space-evenly;
            align-items: center;
            background-color: #fdf5ea;
        
        
        }

        .item_option{
            display: flex;
            justify-content: space-evenly;
            align-items: center;
        }

        .item_paragraph{
            font-family: Arial, Helvetica, sans-serif;
            font-size: 27px;
            font-weight: bold;
        }

        .food_item{
            height: 35vh;
            width: 16vw;
            border-radius: 15px;
            border: 2px solid #111D15;
            box-shadow: 1px 2px 2px 2px #111D15;
            
        }

        .price{
            border-radius: 5px;
            height: 50px;
            width: 70px;
            font-size: 20px;
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


    <!-- Item List -->
    <div id="cater1">
        <div>
            <img src="cater1.png" alt="" class="cater1_pic">
        </div>
        <div>
            <img src="cater2.png" alt="" class="cater1_pic">
        </div>
        <div>
            <img src="cater3.png" alt="" class="cater1_pic">
        </div>
    </div>

    <div style=" padding-top: 20px; height: 45vh; width: 100vw; background-color:#fdf5ea; display: flex; justify-content: space-evenly; padding-top: 100px;">
        <div>
            <h2 style="color: #099468;">About us</h2>
            <p style="font-size: 20px;">Welcome to our one-stop solution for premium services 
                that cater to all your personal <br> and professional needs. We specialize in
                    providing a wide range of services, including <br> event planning, security 
                    solutions, IT support, and much more, ensuring excellence <br> and reliability
                    every step of the way. Whether you're hosting a wedding, birthday, or <br> 
                    special event, we bring elegance and professionalism with our tailored services. 
                    From <br> skilled chefs delivering exquisite culinary experiences to professional
                    photographers <br>
            capturing every precious moment,
                we ensure your celebrations are truly unforgettable. <br><br>  In 
                addition to event services, we prioritize your safety with top-tier security 
                solutions <br> designed to protect your home, business, or special gatherings.our
                mission is to make <br> your life easier and more elegant by offering unparalleled
                services customized to your <br> needs. Let us handle the details while you focus
                on what matters most.</p>
        </div>

        <div>
            <div style="display: flex; justify-content: center; align-items: center; margin-left: 140px; padding: 30px;">
                <div style="display: flex; justify-content: center; align-items: center;">
                    <img src="plate.png" alt="" style="height: 10vh; width: 5vw;">
                </div>
                <div>
                    <h1>10+</h1>
                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: bold;">Experience</p>
                </div>

                <div style="display: flex; justify-content: center; align-items: center; margin-left: 100px;">
                    <img src="man.png" alt="" style="height: 10vh; width: 5vw;">
                </div>
                <div>
                    <h1>20+</h1>
                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: bold;">Employee</p>
                </div>
            </div>
            <div style="display: flex; justify-content: center; align-items: center; margin-right: 100px;">
                <div style="display: flex; justify-content: center; align-items: center;">
                    <img src="clock.png" alt="" style="height: 10vh; width: 5vw;">
                </div>
                <div>
                    <h1>24</h1>
                    <p style="font-family: Arial, Helvetica, sans-serif; font-size: 20px; font-weight: bold;">Completed Work</p>
                </div>
            </div>
        </div>
    </div>

    <div>
        <img src="big_chef.png" alt="" style="width: 100vw; height: 100vh;">
    </div>

    <br>
    <hr>

    <div id="item_block">
        <h1 style="color: #099468; padding-bottom: 20px;">Item List</h1>
        <form method="POST" action="">
            <div class="item_option">
                <?php
               
                $items = [
                    ['name' => 'Item1', 'price' => 5, 'image' => 'cater1.png'],
                    ['name' => 'Item2', 'price' => 8, 'image' => 'cater2.png'],
                    ['name' => 'Item3', 'price' => 10, 'image' => 'cater3.png'],
                ];

                foreach ($items as $index => $item) {
                    echo "<div class='food_item'>
                            <img src='{$item['image']}' alt='' class='cater2_pic'>
                            <p class='item_paragraph'>{$item['name']}</p>
                            <p class='item_paragraph'>\${$item['price']}</p>
                            <input type='number' class='price' name='items[{$index}]' min='0' value='0'>
                            <input type='hidden' name='item_names[{$index}]' value='{$item['name']}'>
                          </div>";
                }
                ?>
            </div>
            <br><br>
            <button type="submit" id="order-button">Order</button>
        </form>
    </div>

    <br>
    <hr>

    <div style=" padding-top: 100px; height: 45vh; width: 100vw; background-color:#fdf5ea; display: flex; justify-content: space-evenly;">
        <div style="margin-top: 80px;">
            <h2 style="color: #099468;">What we offer</h2>
            <p style="font-size: 20px; width: 30vw;">At Premier Plates, we create personalized menus for weddings, 
                corporate events, and private parties, featuring Turkish, Italian, and fusion cuisines. Our 
                services include full-service catering, drop-off catering, and professional staffing to ensure 
                seamless events. We offer elegant hors d'oeuvres, sumptuous main courses, decadent desserts, 
                and a wide selection of beverages. Contact us to make your event extraordinary with our exceptional 
                food and outstanding service</p>
        </div>
        <div>
            <img src="small_chef.png" alt=""style="width: 20vw; height: 40vh;">
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
