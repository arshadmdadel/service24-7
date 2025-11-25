<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Service 24x7</title>
    <style>


        .container {
            max-width: 960px;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9); /* Slight opacity for better readability */
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .logo {
            color: green; /* Logo color green */
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        h1, h2 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }

        .service-section {
            margin-bottom: 40px;
            text-align: center;
        }

        .service-section img {
            width: 100%;
            max-width: 600px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .service-description {
            color: #555;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .service-title {
            color: #333;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .unique-services {
            background: rgba(204, 255, 204, 0.7); /* Light green background to differentiate unique services */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 48px; /* Adding half inch gap between Main Services and Unique Services */
        }

        .slideshow {
            position: relative;
            width: 100%;
            height: 400px;
            margin-bottom: 30px;
        }

        .slides {
            display: none;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        .active {
            display: block;
        }

        .service-description {
            text-align: center;
            margin-bottom: 20px;
        }

        .prev, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            width: auto;
            padding: 16px;
            margin-top: -22px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            border-radius: 0 3px 3px 0;
            user-select: none;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .next {
            right: 0;
            border-radius: 3px 0 0 3px;
        }

        .prev:hover, .next:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }
    </style>
    <script>
        var mainSlideIndex = 0;
        var uniqueSlideIndex = 0;

        function showSlides(className, slideIndex) {
            var slides = document.getElementsByClassName(className);
            if (slideIndex >= slides.length) { slideIndex = 0 }
            if (slideIndex < 0) { slideIndex = slides.length - 1 }
            for (var i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slides[slideIndex].style.display = "block";
            return slideIndex;
        }

        function plusSlides(n, className, slideIndex) {
            slideIndex += n;
            return showSlides(className, slideIndex);
        }

        window.onload = function() {
            mainSlideIndex = showSlides('main-slides', mainSlideIndex);
            uniqueSlideIndex = showSlides('unique-slides', uniqueSlideIndex);
        }
    </script>
</head>
<body>
    <?php include("../header.php") ?> 
    <div class="container">
        <div class="logo">Service 24/7</div> <!-- Logo -->
        <h1>Our Services</h1>

        <h2>Main Services</h2>
        <div class="slideshow">
            <div class="slides main-slides">
                <div class="service-section">
                    <div class="service-title">Electrician Services</div>
                    <img src="elec.jpg" alt="Electrician Services">
                    <div class="service-description">Our electrical experts ensure safety and functionality for all your electrical needs.</div>
                </div>
            </div>
            <div class="slides main-slides">
                <div class="service-section">
                    <div class="service-title">Catering Services</div>
                    <img src="cate.jpg" alt="Catering Services">
                    <div class="service-description">We offer professional catering services to make your events memorable and delightful.</div>
                </div>
            </div>
            <div class="slides main-slides">
                <div class="service-section">
                    <div class="service-title">Cleaning Services</div>
                    <img src="clea.jpg" alt="Cleaning Services">
                    <div class="service-description">Professional cleaning services to keep your home sparkling clean and hygienic.</div>
                </div>
            </div>
            <a class="prev" onclick="mainSlideIndex = plusSlides(-1, 'main-slides', mainSlideIndex)">&#10094;</a>
            <a class="next" onclick="mainSlideIndex = plusSlides(1, 'main-slides', mainSlideIndex)">&#10095;</a>
        </div>

        <h2>Unique Services</h2>
        <div class="unique-services">
            <div class="slideshow">
                <div class="slides unique-slides">
                    <div class="service-section">
                        <div class="service-title">Security Services</div>
                        <img src="secu.jpg" alt="Security Services">
                        <div class="service-description">Our security services ensure the safety and protection of your property and loved ones.</div>
                    </div>
                </div>
                <div class="slides unique-slides">
                    <div class="service-section">
                        <div class="service-title">Babysitting Services</div>
                        <img src="baby.jpg" alt="Babysitting Services">
                        <div class="service-description">Reliable babysitting services to take care of your little ones with love and attention.</div>
                    </div>
                </div>
                <div class="slides unique-slides">
                    <div class="service-section">
                        <div class="service-title">Pet Care Services</div>
                        <img src="pet.jpg" alt="Pet Care Services">
                        <div class="service-description">Professional pet care services to ensure the well-being of your furry friends.</div>
                    </div>
                </div>
                <a class="prev" onclick="uniqueSlideIndex = plusSlides(-1, 'unique-slides', uniqueSlideIndex)">&#10094;</a>
                <a class="next" onclick="uniqueSlideIndex = plusSlides(1, 'unique-slides', uniqueSlideIndex)">&#10095;</a>
            </div>
        </div>
    </div>
    <?php include("../footer.php") ?> 
</body>
</html>
