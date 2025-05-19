<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    header('Location: index.php');
    exit;
}

$logged_in_id = $_SESSION['employee_id'];

// Load employee data
$xml = new DOMDocument;
$xml->load('cict.xml');
$x = $xml->getElementsByTagName('employees')->item(0);
$employees = $x->getElementsByTagName('employee');

$employee_data = null;
foreach($employees as $employee) {
    if($employee->getElementsByTagName('id')->item(0)->nodeValue == $logged_in_id) {
        $employee_data = [
            'firstname' => $employee->getElementsByTagName('firstname')->item(0)->nodeValue,
            'lastname' => $employee->getElementsByTagName('lastname')->item(0)->nodeValue,
            'age' => $employee->getElementsByTagName('age')->item(0)->nodeValue,
            'gender' => $employee->getElementsByTagName('gender')->item(0)->nodeValue,
            'position' => $employee->getElementsByTagName('position')->item(0)->nodeValue,
            'department' => $employee->getElementsByTagName('department')->item(0)->nodeValue,
            'email' => $employee->getElementsByTagName('email')->item(0)->nodeValue,
            'phone' => $employee->getElementsByTagName('phone')->item(0)->nodeValue,
            'address' => $employee->getElementsByTagName('address')->item(0)->nodeValue,
            'emergency_contact' => $employee->getElementsByTagName('emergency_contact')->item(0)->nodeValue,
            'image' => $employee->getElementsByTagName('image')->item(0)->nodeValue
        ];
        break;
    }
}

if (!$employee_data) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .navbar {
            background-color: #2c3e50;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-links {
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }
        .nav-links a:hover {
            color: #3498db;
        }
        .form-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: #3498db;
            outline: none;
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        .logout-btn {
            background-color: #e74c3c;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
        .submit-btn {
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        .submit-btn:hover {
            background-color: #2980b9;
        }
        .image-input-container {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }
        .preview-container {
            text-align: center;
            margin-top: 10px;
        }
        .preview-image {
            max-width: 150px;
            max-height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #3498db;
        }
        .current-image {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-links">
            <a href="dashboard.php">Home</a>
            <a href="edit.php">Edit Profile</a>
            <a href="delete.php?id=<?php echo htmlspecialchars($_SESSION['employee_id']); ?>">Delete Account</a>
            <a href="aboutus.html">About</a>
        </div>
        <div class="user-info">
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="form-container">
        <h1 style="text-align: center; margin-bottom: 30px; color: #2c3e50;">Edit Profile</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="firstname">First Name:</label>
                    <input type="text" name="firstname" value="<?php echo htmlspecialchars($employee_data['firstname']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="lastname">Last Name:</label>
                    <input type="text" name="lastname" value="<?php echo htmlspecialchars($employee_data['lastname']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="age">Age:</label>
                    <input type="number" name="age" min="18" max="100" value="<?php echo htmlspecialchars($employee_data['age']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select name="gender" required>
                        <option value="Male" <?php echo $employee_data['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $employee_data['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo $employee_data['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="position">Position:</label>
                    <input type="text" name="position" value="<?php echo htmlspecialchars($employee_data['position']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="department">Department:</label>
                    <input type="text" name="department" value="<?php echo htmlspecialchars($employee_data['department']); ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($employee_data['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Contact Number:</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($employee_data['phone']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <textarea name="address" rows="3" required><?php echo htmlspecialchars($employee_data['address']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="emergency_contact">Emergency Contact:</label>
                <input type="text" name="emergency_contact" value="<?php echo htmlspecialchars($employee_data['emergency_contact']); ?>" required>
            </div>

            <div class="form-group">
                <label>Employee Photo:</label>
                <div class="current-image">
                    <img src="<?php echo htmlspecialchars($employee_data['image']); ?>" alt="Current Photo" class="preview-image" onerror="this.src='img/default-avatar.png'">
                </div>
                <div class="image-input-container">
                    <input type="file" name="image_file" accept="image/*" id="imageFile" onchange="previewImage(this)">
                </div>
                <div class="preview-container">
                    <img id="preview" class="preview-image" style="display: none;" alt="New Photo Preview">
                </div>
            </div>

            <button type="submit" name="submit" class="submit-btn">Update Profile</button>
        </form>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <?php
    if(isset($_POST['submit'])) {
        $xml = new DOMDocument;
        $xml->load('cict.xml');
        $x = $xml->getElementsByTagName('employees')->item(0);
        $employees = $x->getElementsByTagName('employee');

        foreach($employees as $employee) {
            if($employee->getElementsByTagName('id')->item(0)->nodeValue == $logged_in_id) {
                // Update employee information
                $employee->getElementsByTagName('firstname')->item(0)->nodeValue = $_POST['firstname'];
                $employee->getElementsByTagName('lastname')->item(0)->nodeValue = $_POST['lastname'];
                $employee->getElementsByTagName('age')->item(0)->nodeValue = $_POST['age'];
                $employee->getElementsByTagName('gender')->item(0)->nodeValue = $_POST['gender'];
                $employee->getElementsByTagName('position')->item(0)->nodeValue = $_POST['position'];
                $employee->getElementsByTagName('department')->item(0)->nodeValue = $_POST['department'];
                $employee->getElementsByTagName('email')->item(0)->nodeValue = $_POST['email'];
                $employee->getElementsByTagName('phone')->item(0)->nodeValue = $_POST['phone'];
                $employee->getElementsByTagName('address')->item(0)->nodeValue = $_POST['address'];
                $employee->getElementsByTagName('emergency_contact')->item(0)->nodeValue = $_POST['emergency_contact'];

                // Handle image upload
                if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
                    $file = $_FILES['image_file'];
                    $fileName = $logged_in_id . '_' . time() . '_' . basename($file['name']);
                    $targetPath = 'covers/' . $fileName;
                    
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        // Delete old image if it exists
                        $oldImage = $employee->getElementsByTagName('image')->item(0)->nodeValue;
                        if (file_exists($oldImage) && $oldImage != 'img/default-avatar.png') {
                            unlink($oldImage);
                        }
                        $employee->getElementsByTagName('image')->item(0)->nodeValue = $targetPath;
                    }
                }

                // Update session name
                $_SESSION['employee_name'] = $_POST['firstname'] . ' ' . $_POST['lastname'];
                
                // Save XML
                $xml->save('cict.xml');
                
                echo "<script>alert('Profile updated successfully!'); window.location.href='dashboard.php';</script>";
                exit;
            }
        }
    }
    ?>
</body>
</html> 