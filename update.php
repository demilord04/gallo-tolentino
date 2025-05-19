<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Employee</title>
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
        .current-image {
            text-align: center;
            margin-bottom: 20px;
        }
        .current-image img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #3498db;
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
            display: none;
            border: 3px solid #3498db;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-links">
            <a href="index.php">Dashboard</a>
            <a href="add.php">Add Employee</a>
            <a href="edit.php">Edit Employee</a>
            <a href="aboutus.html">About</a>
        </div>
    </nav>

    <div class="form-container">
        <h1 style="text-align: center; margin-bottom: 30px; color: #2c3e50;">Update Employee</h1>
        
        <?php
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $xml = new DOMDocument;
            $xml->load('cict.xml');
            $x = $xml->getElementsByTagName('employees')->item(0);
            $employees = $x->getElementsByTagName('employee');
            
            foreach($employees as $employee) {
                if($employee->getElementsByTagName('id')->item(0)->nodeValue == $id) {
                    $firstname = $employee->getElementsByTagName('firstname')->item(0)->nodeValue;
                    $lastname = $employee->getElementsByTagName('lastname')->item(0)->nodeValue;
                    $age = $employee->getElementsByTagName('age')->item(0)->nodeValue;
                    $gender = $employee->getElementsByTagName('gender')->item(0)->nodeValue;
                    $position = $employee->getElementsByTagName('position')->item(0)->nodeValue;
                    $department = $employee->getElementsByTagName('department')->item(0)->nodeValue;
                    $email = $employee->getElementsByTagName('email')->item(0)->nodeValue;
                    $phone = $employee->getElementsByTagName('phone')->item(0)->nodeValue;
                    $address = $employee->getElementsByTagName('address')->item(0)->nodeValue;
                    $emergency_contact = $employee->getElementsByTagName('emergency_contact')->item(0)->nodeValue;
                    $image = $employee->getElementsByTagName('image')->item(0)->nodeValue;
                    ?>
                    <div class="current-image">
                        <img src="<?php echo $image; ?>" alt="Current Photo" id="currentImage" onerror="this.src='img/default-avatar.png'">
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstname">First Name:</label>
                                <input type="text" name="firstname" value="<?php echo $firstname; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="lastname">Last Name:</label>
                                <input type="text" name="lastname" value="<?php echo $lastname; ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="age">Age:</label>
                                <input type="number" name="age" min="18" max="100" value="<?php echo $age; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select name="gender" required>
                                    <option value="Male" <?php echo $gender == 'Male' ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo $gender == 'Female' ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo $gender == 'Other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="position">Position:</label>
                                <input type="text" name="position" value="<?php echo $position; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="department">Department:</label>
                                <input type="text" name="department" value="<?php echo $department; ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" value="<?php echo $email; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Contact Number:</label>
                                <input type="tel" name="phone" value="<?php echo $phone; ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Address:</label>
                            <textarea name="address" rows="3" required><?php echo htmlspecialchars($address); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="emergency_contact">Emergency Contact:</label>
                            <input type="text" name="emergency_contact" value="<?php echo $emergency_contact; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Employee Photo:</label>
                            <div class="image-input-container">
                                <input type="file" name="image_file" accept="image/*" id="imageFile" onchange="previewImage(this)">
                            </div>
                            <div class="preview-container">
                                <img id="preview" class="preview-image" alt="Preview">
                            </div>
                        </div>

                        <button type="submit" name="submit" class="submit-btn">Update Employee</button>
                    </form>
                    <?php
                    break;
                }
            }
        }

        if(isset($_POST['submit'])) {
            $id = $_POST['id'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $age = $_POST['age'];
            $gender = $_POST['gender'];
            $position = $_POST['position'];
            $department = $_POST['department'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $emergency_contact = $_POST['emergency_contact'];
            
            // Handle image upload
            $imageValue = '';
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
                $file = $_FILES['image_file'];
                $fileName = $id . '_' . time() . '_' . basename($file['name']);
                $targetPath = 'covers/' . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $imageValue = $targetPath;
                }
            }
            
            $xml = new DOMDocument;
            $xml->load('cict.xml');
            $x = $xml->getElementsByTagName('employees')->item(0);
            $employees = $x->getElementsByTagName('employee');
            
            foreach($employees as $employee) {
                if($employee->getElementsByTagName('id')->item(0)->nodeValue == $id) {
                    $employee->getElementsByTagName('firstname')->item(0)->nodeValue = $firstname;
                    $employee->getElementsByTagName('lastname')->item(0)->nodeValue = $lastname;
                    $employee->getElementsByTagName('age')->item(0)->nodeValue = $age;
                    $employee->getElementsByTagName('gender')->item(0)->nodeValue = $gender;
                    $employee->getElementsByTagName('position')->item(0)->nodeValue = $position;
                    $employee->getElementsByTagName('department')->item(0)->nodeValue = $department;
                    $employee->getElementsByTagName('email')->item(0)->nodeValue = $email;
                    $employee->getElementsByTagName('phone')->item(0)->nodeValue = $phone;
                    $employee->getElementsByTagName('address')->item(0)->nodeValue = $address;
                    $employee->getElementsByTagName('emergency_contact')->item(0)->nodeValue = $emergency_contact;
                    
                    if (!empty($imageValue)) {
                        // Delete old image if it exists
                        $oldImage = $employee->getElementsByTagName('image')->item(0)->nodeValue;
                        if (!empty($oldImage) && file_exists($oldImage)) {
                            unlink($oldImage);
                        }
                        $employee->getElementsByTagName('image')->item(0)->nodeValue = $imageValue;
                    }
                    
                    $xml->save('cict.xml');
                    echo "<script>alert('Employee updated successfully!'); window.location.href='index.php';</script>";
                    break;
                }
            }
        }
        ?>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const currentImage = document.getElementById('currentImage');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    currentImage.style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>