<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "service24/7";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nid = $_POST['nid'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $work_type = $_POST['work_type'];
    $expertise = $_POST['expertise'];
    $detail = $_POST['detail'];
    $rating = 0; 
    $num_of_rating = 0; 
    $price = $_POST['price'];

    // Handle photo upload
    $photo = $_FILES['photo']['name'];
    $target_dir = "uploads/"; // Folder to store the uploaded images
    $target_file = $target_dir . basename($photo);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the uploaded file is an image
    if (isset($_FILES['photo'])) {
        $check = getimagesize($_FILES['photo']['tmp_name']);
        if ($check !== false) {
            // File is an image
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

    // Allow certain file formats
    if ($uploadOk == 1 && !in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if upload is successful
    if ($uploadOk == 1 && move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
        // File is uploaded successfully
    } else {
        echo "Sorry, there was an error uploading your file.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        // Insert into worker table including photo path
        $sql = "INSERT INTO worker (nid, fullname, phone, email, password, work_type, expertise, detail, rating, num_of_rating, price, photo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssiiisd", $nid, $fullname, $phone, $email, $password, $work_type, $expertise, $detail, $rating, $num_of_rating, $price, $target_file);

        if ($stmt->execute()) {
            echo "Registration successful";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Registration</title>
    
    <style>
        body {
            background: #f5f5dc; /* Creamy background */
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .container {
            width: 100%;
            max-width: 600px;
            background: #ccffcc; /* Light green background */
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 30px auto;
            text-align: center;
        }

        h2 {
            font-size: 26px;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            width: 100%;
            text-align: left;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input, select, textarea, button {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        textarea {
            resize: none;
            height: 100px;
        }

        .input-group {
            margin-bottom: 20px;
        }
    </style>
    <script>
        function updateExpertiseOptions() {
            var workType = document.getElementById('work_type').value;
            var expertiseField = document.getElementById('expertise_field');
            var expertiseSelect = document.getElementById('expertise');

            expertiseSelect.innerHTML = ''; // Clear previous options
            if (workType == '1') { // Electrician
                expertiseField.style.display = 'block';
                expertiseSelect.innerHTML += '<option value="Wiring">Wiring</option>';
                expertiseSelect.innerHTML += '<option value="Appliance Repair">Appliance Repair</option>';
                expertiseSelect.innerHTML += '<option value="Lighting">Lighting</option>';
            } else if (workType == '2') { // Catering
                expertiseField.style.display = 'block';
                expertiseSelect.innerHTML += '<option value="Italian">Italian</option>';
                expertiseSelect.innerHTML += '<option value="Chinese">Chinese</option>';
                expertiseSelect.innerHTML += '<option value="Thai">Thai</option>';
            } else {
                expertiseField.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <?php include("../header.php")?>
    <div class="container">
        <h2>Worker Registration Form</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="input-group">
                <label for="photo">Upload Your Picture:</label>
                <input type="file" id="photo" name="photo" accept="image/*" required>
            </div>
            
            <div class="input-group">
                <label for="nid">National ID:</label>
                <input type="text" id="nid" name="nid" required>
            </div>

            <div class="input-group">
                <label for="fullname">Full Name:</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>

            <div class="input-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" required>
            </div>

            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="input-group">
                <label for="work_type">Work Type:</label>
                <select id="work_type" name="work_type" onchange="updateExpertiseOptions()" required>
                    <option value="1">Electrician</option>
                    <option value="2">Catering</option>
                    <option value="5">Babysitting</option>
                    <option value="0">Pet Care</option>
                    <option value="4">Security</option>
                    <option value="3">Cleaning</option>
                </select>
            </div>

            <div id="expertise_field" class="input-group" style="display: none;">
                <label for="expertise">Choose Your Expertise:</label>
                <select id="expertise" name="expertise" required>
                    <!-- Options will be added dynamically based on Work Type -->
                </select>
            </div>

            <div class="input-group">
                <label for="detail">Detail:</label>
                <textarea id="detail" name="detail" required></textarea>
            </div>

            <div class="input-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" required>
            </div>

            <button type="submit">Register</button>
        </form>
    </div>
    <?php include("footer.php")?> <br>

</body>
</html>
