<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Team - Service 24x7</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, #ccffcc, #99ff99);
            background-size: cover;
            color: #333;
        }

        .container {
            max-width: 960px;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .logo {
            color: #228B22;
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        h1 {
            color: #228B22;
            text-align: center;
            margin-bottom: 40px;
        }

        .team-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .team-member {
            background-color: #f0fff0;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        .team-member img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin-bottom: 15px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .team-member h3 {
            color: #006400;
            font-size: 20px;
            margin: 10px 0;
        }

        .team-member p {
            color: #555;
            font-size: 14px;
            line-height: 1.6;
        }

        .team-member .role {
            color: #228B22;
            font-size: 16px;
            margin-bottom: 15px;
            font-style: italic;
        }

        .team-member .contact {
            margin-top: 10px;
            font-size: 14px;
            color: #006400;
        }

        .team-member .contact a {
            text-decoration: none;
            color: #004d00;
            font-weight: bold;
        }

        .team-member .contact a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .team-section {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .team-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include("../header.php") ?> 
    <div class="container">
        <div class="logo">Service 24/7</div>
        <h1>Meet Our Dedicated Team</h1>
        
        <div class="team-section">
            <div class="team-member">
                <img src="arshad.jpeg" alt="Arshad Mohammad Adel">
                <h3>Arshad Mohammad Adel</h3>
                <div class="role">Founder & CEO</div>
                <p>Arshad is the visionary behind Service 24x7, leading the team with a passion for innovation and customer satisfaction.</p>
                <div class="contact">Email: <a href="milto:adel@example.com">adel@example.com</a></div>
            </div>
            <div class="team-member">
                <img src="mahi.jpeg" alt="Mahi Hoque">
                <h3>Mahi Hoque</h3>
                <div class="role">Chief Operations Officer</div>
                <p>Mahi ensures seamless operations, focusing on delivering top-notch services to our clients with precision and care.</p>
                <div class="contact">Email: <a href="milto:mahi@example.com">mahi@example.com</a></div>
            </div>
            <div class="team-member">
                <img src="shaon.jpeg" alt="Shaon Khan ">
                <h3>Shaon Khan</h3>
                <div class="role">Head of Marketing</div>
                <p>Shaon drives our marketing strategies, spreading the word about our exceptional services and building strong client relationships.</p>
                <div class="contact">Email: <a href="mailto:shaon@example.com">shaon@example.com</a></div>
            </div>
            <div class="team-member">
                <img src="shoyeb.jpg" alt="Shoyeb Ali Khan">
                <h3>Shoyeb Ali Khan</h3>
                <div class="role">Technical Lead</div>
                <p>Shoyeb leads our technical team, ensuring that we leverage the best tools and technology to serve our clients efficiently.</p>
                <div class="contact">Email: <a href="mailto:shoyeb@example.com">shoyeb@example.com</a></div>
            </div>
            <div class="team-member">
                <img src="images/team_member5.jpg" alt="Saif">
                <h3>Saif</h3>
                <div class="role">Customer Success Manager</div>
                <p>Saif is dedicated to ensuring our clients are happy and supported, building strong and lasting relationships.</p>
                <div class="contact">Email: <a href="mailto:saif@example.com">saif@example.com</a></div>
            </div>
        </div>
    </div>
    <?php include("../footer.php") ?> 
</body>
</html>
