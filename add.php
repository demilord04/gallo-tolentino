<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['employee_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Get the next available ID
$nextId = 1; // Default ID if no employees exist
$xml = new DOMDocument();
if (file_exists('cict.xml')) {
    $xml->load('cict.xml');
    $employees = $xml->getElementsByTagName('employee');
    $lastId = 0;
    foreach ($employees as $employee) {
        $id = intval($employee->getElementsByTagName('id')->item(0)->nodeValue);
        if ($id > $lastId) {
            $lastId = $id;
        }
    }
    $nextId = $lastId + 1;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Validate required fields
    $required_fields = ['id', 'name', 'email', 'password', 'confirm_password', 'department', 'position'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst($field) . " is required.";
        }
    }
    
    // Validate email format
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    // Validate password match
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors[] = "Passwords do not match.";
    }
    
    // Validate ID format (must be 8 digits)
    if (!empty($_POST['id']) && !preg_match('/^\d{8}$/', $_POST['id'])) {
        $errors[] = "ID must be exactly 8 digits.";
    }
    
    // Check if ID already exists
    if (empty($errors)) {
        $xml = new DOMDocument();
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;
        
        if (file_exists('cict.xml')) {
            $xml->load('cict.xml');
            $employees = $xml->getElementsByTagName('employee');
            foreach ($employees as $employee) {
                if ($employee->getElementsByTagName('id')->item(0)->nodeValue == $_POST['id']) {
                    $errors[] = "ID already exists.";
                    break;
                }
            }
        } else {
            // Create new XML structure if file doesn't exist
            $root = $xml->createElement('employees');
            $xml->appendChild($root);
        }
        
        if (empty($errors)) {
            // Handle image upload
            $imagePath = 'img/default-avatar.png'; // Default image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'img/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                
                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = uniqid() . '.' . $fileExtension;
                    $uploadFile = $uploadDir . $newFileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                        $imagePath = $uploadFile;
                    } else {
                        $errors[] = "Failed to upload image.";
                    }
                } else {
                    $errors[] = "Invalid image format. Only JPG, JPEG, and PNG are allowed.";
                }
            }
            
            if (empty($errors)) {
                // Create new employee element
                $employee = $xml->createElement('employee');
                
                // Add employee details
                $fields = ['id', 'name', 'email', 'password', 'department', 'position'];
                foreach ($fields as $field) {
                    $element = $xml->createElement($field);
                    $element->appendChild($xml->createTextNode($_POST[$field]));
                    $employee->appendChild($element);
                }
                
                // Add image path
                $imageElement = $xml->createElement('image');
                $imageElement->appendChild($xml->createTextNode($imagePath));
                $employee->appendChild($imageElement);
                
                // Add employee to XML
                $root = $xml->getElementsByTagName('employees')->item(0);
                $root->appendChild($employee);
                
                // Save XML with proper formatting
                $xml->formatOutput = true;
                $xml->preserveWhiteSpace = false;
                if ($xml->save('cict.xml')) {
                    header('Location: index.php');
                    exit;
                } else {
                    $errors[] = "Failed to save employee data.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Sign Up</title>
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
            display: none;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #3498db;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .id-notice {
            background-color: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #b8daff;
        }
        .id-number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-links">
            <a href="index.php">Login</a>
            <a href="add.php">Sign Up</a>
            <a href="aboutus.html">About</a>
        </div>
    </nav>

    <div class="form-container">
        <h1 style="text-align: center; margin-bottom: 30px; color: #2c3e50;">Employee Sign Up</h1>
        
        <div class="id-notice">
            <p>Your Employee ID will be:</p>
            <div class="id-number"><?php echo str_pad($nextId, 8, '0', STR_PAD_LEFT); ?></div>
            <p>Please remember this ID number for future login.</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="firstname">First Name:</label>
                    <input type="text" name="firstname" required>
                </div>
                <div class="form-group">
                    <label for="lastname">Last Name:</label>
                    <input type="text" name="lastname" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="age">Age:</label>
                    <input type="number" name="age" min="18" max="100" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="position">Position:</label>
                    <input type="text" name="position" required>
                </div>
                <div class="form-group">
                    <label for="department">Department:</label>
                    <input type="text" name="department" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Contact Number:</label>
                    <input type="tel" name="phone" required>
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <textarea name="address" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="emergency_contact">Emergency Contact:</label>
                <input type="text" name="emergency_contact" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" required>
                </div>
            </div>

            <div class="form-group">
                <label>Employee Photo:</label>
                <div class="image-input-container">
                    <input type="file" name="image" accept="image/*" id="imageFile" onchange="previewImage(this)">
                </div>
                <div class="preview-container">
                    <img id="preview" class="preview-image" alt="Employee Photo Preview">
                </div>
            </div>

            <button type="submit" name="submit" class="submit-btn">Sign Up</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="index.php">Login here</a>
        </div>
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
        // Validate passwords match
        if ($_POST['password'] !== $_POST['confirm_password']) {
            echo "<script>alert('Passwords do not match!');</script>";
            exit;
        }

        $xml = new DOMDocument;
        $xml->load('cict.xml');
        $x = $xml->getElementsByTagName('employees')->item(0);
        
        // Get the last ID and increment it
        $lastId = 0;
        $employees = $x->getElementsByTagName('employee');
        foreach($employees as $employee) {
            $id = intval($employee->getElementsByTagName('id')->item(0)->nodeValue);
            if($id > $lastId) {
                $lastId = $id;
            }
        }
        $newId = $lastId + 1;

        $employee = $xml->createElement('employee');
        
        $id = $xml->createElement('id', $newId);
        $firstname = $xml->createElement('firstname', $_POST['firstname']);
        $lastname = $xml->createElement('lastname', $_POST['lastname']);
        $age = $xml->createElement('age', $_POST['age']);
        $gender = $xml->createElement('gender', $_POST['gender']);
        $position = $xml->createElement('position', $_POST['position']);
        $department = $xml->createElement('department', $_POST['department']);
        $email = $xml->createElement('email', $_POST['email']);
        $phone = $xml->createElement('phone', $_POST['phone']);
        $address = $xml->createElement('address', $_POST['address']);
        $emergency_contact = $xml->createElement('emergency_contact', $_POST['emergency_contact']);
        $password = $xml->createElement('password', $_POST['password']);
        
        // Handle image upload
        $imageValue = 'img/default-avatar.png'; // Default image
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $file = $_FILES['image'];
            $fileName = $newId . '_' . time() . '_' . basename($file['name']);
            $targetPath = 'covers/' . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $imageValue = $targetPath;
            }
        }
        $image = $xml->createElement('image', $imageValue);
        
        $employee->appendChild($id);
        $employee->appendChild($firstname);
        $employee->appendChild($lastname);
        $employee->appendChild($age);
        $employee->appendChild($gender);
        $employee->appendChild($position);
        $employee->appendChild($department);
        $employee->appendChild($email);
        $employee->appendChild($phone);
        $employee->appendChild($address);
        $employee->appendChild($emergency_contact);
        $employee->appendChild($password);
        $employee->appendChild($image);
        
        $x->appendChild($employee);
        $xml->save('cict.xml');
        
        echo "<script>alert('Account created successfully! Please login.'); window.location.href='index.php';</script>";
        exit;
    }
    ?>
</body>
</html>
