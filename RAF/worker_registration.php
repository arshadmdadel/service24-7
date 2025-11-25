<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the uploaded picture
    $picture_name = basename($_FILES['picture']['name']);
    $picture_tmp = $_FILES['picture']['tmp_name'];
    $picture_folder = 'uploads/' . $picture_name;

    // Move the uploaded file to the target folder
    if (move_uploaded_file($picture_tmp, $picture_folder)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO worker (nid, fullname, phone, email, password, work_type, detail, price, picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['nid'],
                $_POST['fullname'],
                $_POST['phone'],
                $_POST['email'],
                password_hash($_POST['password'], PASSWORD_BCRYPT),
                $_POST['work_type'],
                $_POST['detail'],
                $_POST['price'],
                $picture_name // Set this appropriately
            ]);
            
            if ($stmt->rowCount() > 0) {
                echo "Worker inserted successfully.";
            } else {
                echo "Insertion failed.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
    } else {
        echo "<p class='error'>Failed to upload the picture.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        form input, form select, form textarea, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            background: #4CAF50;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        form button:hover {
            background: #45a049;
        }
        .success {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Worker Registration</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="nid">NID:</label>
            <input type="number" name="nid" id="nid" required>

            <label for="fullname">Full Name:</label>
            <input type="text" name="fullname" id="fullname" required>

            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <label for="work_type">Work Type:</label>
            <select name="work_type" id="work_type" required>
                <option value="0">Pet caring</option>
                <option value="1">Electrician</option>
                <option value="3">Cleaning</option>
                <option value="4">Babysitting</option>
                <option value="5">Security</option>
                <option value="2">Catering</option>
            </select>

            <label for="detail">Detail:</label>
            <textarea name="detail" id="detail" required></textarea>

            <label for="price">Price:</label>
            <input type="number" name="price" id="price" required>

            <label for="picture">Picture:</label>
            <input type="file" name="picture" id="picture" accept="image/*" required>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
