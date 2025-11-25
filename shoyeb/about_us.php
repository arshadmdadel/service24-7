<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Service 24x7</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, #ccffcc, #99ff99); /* Green gradient background */
            background-size: cover;
            background-color: #f0f0f0;
        }

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

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }

        p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .mission-vision {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .mission, .vision {
            flex: 1;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 0 10px;
        }

        .mission h3, .vision h3 {
            color: #333;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include("../header.php") ?> 
    <div class="container">
        <div class="logo">Service 24/7</div> <!-- Logo -->
        <h1>About Us</h1>
        <p>
            Service 24x7 is your one-stop solution for all your home service needs. 
            We connect you with skilled and reliable professionals for a wide range of services, 
            from plumbing and electrical work to cleaning and home repairs. 
            Our platform aims to make your life easier by providing convenient access 
            to quality services at your fingertips.
        </p>

        <div class="mission-vision">
            <div class="mission">
                <h3>Our Mission</h3>
                <p>
                    To empower homeowners by providing easy access to a network of skilled professionals, 
                    ensuring convenient and reliable home services at competitive prices.
                </p>
            </div>
            <div class="vision">
                <h3>Our Vision</h3>
                <p>
                    To become the leading platform for home services, connecting homeowners with trusted professionals 
                    and building a thriving community that supports both service providers and customers.
                </p>
            </div>
        </div>
    </div>
    <?php include("../footer.php") ?> 
</body>
</html>
