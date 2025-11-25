
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
            <a href="" class="A">Home</a>
            <a href="" class="A">Review</a>
            <a href="" class="A">Contact</a>
            <select style="border: none; font-size: 20px; background-color: #fff; color: #099468; font-family: 'Times New Roman', Times, serif;"
                name="" id="">
                <option class="Service" value="" selected disabled>Service</option>
                <option class="Service" value="">Cleaning</option>
                <option class="Service" value="">Catering</option>
                <option class="Service" value="">Electrician</option>
                <option class="Service" value="">Security</option>
                <option class="Service" value="">Baby Sitting</option>
                <option class="Service" value="">Pet Caring</option>
            </select>
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

</body>
</html>

