


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing-page</title>
    <link rel="stylesheet" href="home_page_style.css">
    <!-- linking swiperjs css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <style>
#notification-modal ul {
    padding: 0;
    margin: 0;
}

#notification-modal li {
    padding: 0px;
    border-bottom: 1px solid #ddd;
}

#notification-modal li:last-child {
    border-bottom: none;
}




    </style>
</head>

<body>

<?php include("header.php") ?> 


    <div id="main1">

        <div id="main1_L">
            <p style="font-size: 25px; font-family: Arial, Helvetica, sans-serif;">Quality service at a fair price</p>
            <h1 style="color: #099468; font-size: 60px; font-family: Arial, Helvetica, sans-serif;  margin-top: 25px;">
                Your One-Stop <br> Solution for Every <br> Service Needed</h1>
            <p style="font-size: 25px; margin-top: 25px; font-family: Arial, Helvetica, sans-serif;">We provide
                Performing tasks using the least <br>amount of time, energy, and money</p>
        </div>
        <div id="main1_R"></div>

    </div>

    <div id="main2">

        <div id="main2_L">
            <h2 style=" font-size: 45px; font-family: Arial, Helvetica, sans-serif; vertical-align: middle;">We always
                provide the <br> best service</h2>
        </div>
        <div id="main2_R">
            <h2>Service</h2>
            <p style="font-size: 25px; margin-top: 15px; font-family: Arial, Helvetica, sans-serif;">We provide
                Performing tasks using the least <br>amount of time, energy, and money</p>
        </div>

    </div>
    <hr id="mhr">

    <div id="main3">
        <div class="swiper">
            <div class="slider-wrapper">
                <div class="card-list swiper-wrapper">
                    <div class="card-item swiper-slide">
                        <img src="image/Srv-clean.jpg" alt="User Image" class="user-image" />
                        <h2 class="user-name">Cleaning</h2>
                        <p class="user-profession">While we can customize your cleaning plan to suit your needs, most clients schedule regular cleaning services</p>
                        <button class="message-button">Book now <img src="image/Arrow.png" alt=""></button>
                    </div>
                    <div class="card-item swiper-slide">
                        <img src="image/Srv-clean.jpg" alt="User Image" class="user-image" />
                        <h2 class="user-name">Catering</h2>
                        <p class="user-profession">Frontend Developer</p>
                        <button class="message-button">Book now <img src="image/Arrow.png" alt=""></button>
                    </div>
                    <div class="card-item swiper-slide">
                        <img src="image/Srv-clean.jpg" alt="User Image" class="user-image" />
                        <h2 class="user-name">Electrician</h2>
                        <p class="user-profession">Experience the peace of mind that comes with reliable electrical services. Our skilled electricians handle all your electrical needs, from installations and repairs to maintenance and troubleshooting.</p>
                        <button class="message-button">Book now <img src="image/Arrow.png" alt=""></button>
                    </div>
                    <div class="card-item swiper-slide">
                        <img src="image/Srv-clean.jpg" alt="User Image" class="user-image" />
                        <h2 class="user-name">Secuirity</h2>
                        <p class="user-profession">Your safety, our priority. With a vigilant security guard on duty, rest easy knowing your property is protected. Reliable, alert, and always ready—peace of mind starts here </p>
                        <button class="message-button">Book now <img src="image/Arrow.png" alt=""></button>
                    </div>
                    <div class="card-item swiper-slide">
                        <img src="image/baby.jpg" alt="User Image" class="user-image" />
                        <h2 class="user-name">Baby Sitting</h2>
                        <p class="user-profession">Peace of mind starts with trusted care. Our experienced caregivers provide a nurturing and stimulating environment for your little ones, allowing you to focus on your day with confidence.</p>
                        <button class="message-button">Book now <img src="image/Arrow.png" alt=""></button>
                    </div>
                    <div class="card-item swiper-slide">
                        <img src="image/OIP.jpg" alt="User Image" class="user-image" />
                        <h2 class="user-name">Pet Caring</h2>
                        <p class="user-profession">Spoil your furry friends with love and care! Our dedicated pet sitters provide top-notch attention, ensuring your pets are happy and safe while you're away.</p>
                        <button class="message-button">Book now <img src="image/Arrow.png" alt=""></button>
                    </div>
                </div>
    
                <div class="swiper-pagination"></div>
                <div class=" swiper-slider-button swiper-button-prev"></div>
                <div class=" swiper-slider-button swiper-button-next"></div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="script.js"></script>


    <div id="main4">
        <div id="main4_L">
            <p style="font-size: 17px;">Affordable solutions</p>
            <p style="font-size: 50px; font-weight: bold; margin-top: 10px; color: #099468;">High-Quality and Friendly <br>
                Services at Fair Prices</p>
            <p style="font-size: 17px; margin-top: 10px; color: rgb(142, 133, 133);">
                We provide comprehensive services tailored to your needs. 
                <br> From residential cleaning  services
            </p>
        </div>
        <div id="main4_R">
            <img src="image/cartoon.png" alt="" id="cartoon">
        </div>
    </div>

    <div id="main5">
        <div id="main5-L" style="display: flex;  align-items: center;">
            <img src="image/chef.png" alt="" style="height: 90%; width: 70%; padding-left: 200px; border-radius: 10px;">
        </div>
        <div id="main5-R">
            <div style="font-size: 40px; font-weight: bold;">
                <p>Welcome To Our</p>
                <p>Service 24/7 Company!</p>
            </div>

            <p style="margin-top: 10px; color: #666666;">We make your space shine! Professional and reliable service <br> company providing top-notch solutions for
                homes and businesses. <br> Satisfaction guaranteed!"</p>

            <div style=" width: 70%; display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                <div style="border-radius: 5px; padding-left: 5px; display: flex; align-items: center; height: 40px; width:40% ; background-color: #F5F4F4;" ><img src="image/Frame 1310.png" alt=""><label for="">Vetted professionals
                </label></div>
                <div style="border-radius: 5px; padding-left: 5px; display: flex; align-items: center; height: 40px; width:40% ; background-color: #F5F4F4;" ><img src="image/Frame 1310.png" alt=""><label for="">Affordable Prices
                </label></div>
            </div>    
            <div style="width: 70%; display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                <div style="border-radius: 5px; padding-left: 5px; display: flex; align-items: center; height: 40px; width:40% ; background-color: #F5F4F4;" ><img src="image/Frame 1310.png" alt=""><label for="">Next day availability
                </label></div>
                <div style="border-radius: 5px; padding-left: 5px; display: flex; align-items: center; height: 40px; width:40% ; background-color: #F5F4F4;" ><img src="image/Frame 1310.png" alt=""><label for="">Best Quality
                </label></div>
            </div>   
            <div style="width: 70%; display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                <div style="border-radius: 5px; padding-left: 5px; display: flex; align-items: center; height: 40px; width:40% ; background-color: #F5F4F4;" ><img src="image/Frame 1310.png" alt=""><label for="">Standard cleaning tasks
                </label></div>
                <div style="border-radius: 5px; padding-left: 5px; display: flex; align-items: center; height: 40px; width:40% ; background-color: #F5F4F4;" ><img src="image/Frame 1310.png" alt=""><label for="">Affordable Prices
                </label></div>
            </div>   

        </div>
    </div>


    <?php include("footer.php") ?>








</body>

</html>