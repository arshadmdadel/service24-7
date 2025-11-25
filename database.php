<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "service24/7";

$conn = mysqli_connect($servername, $username, $password, $database);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "service24/7  Connected successfully";

?>