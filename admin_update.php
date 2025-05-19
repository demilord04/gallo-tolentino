<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Check if employee ID is provided
if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$employee_id = $_GET['id'];

// Load employee data
$xml = new DOMDocument;
$xml->load('cict.xml');
$x = $xml->getElementsByTagName('employees')->item(0);
$employees = $x->getElementsByTagName('employee');

$employee_data = null;
foreach($employees as $employee) {
    if($employee->getElementsByTagName('id')->item(0)->nodeValue == $employee_id) {
        $employee_data = [
            'id' => $employee_id,
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
            'image' => $employee->getElementsByTagName('image')->item(0)->nodeValue,
            'password' => $employee->getElementsByTagName('password')->item(0)->nodeValue
        ];
        break;
    }
}

if (!$employee_data) {
    header('Location: admin_dashboard.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $xml = new DOMDocument;
    $xml->load('cict.xml');
    $x = $xml->getElementsByTagName('employees')->item(0);
    $employees = $x->getElementsByTagName('employee');

    foreach($employees as $employee) {
        if($employee->getElementsByTagName('id')->item(0)->nodeValue == $employee_id) {
            // Update employee data
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
            
            // Update password if provided
            if (!empty($_POST['password'])) {
                $employee->getElementsByTagName('password')->item(0)->nodeValue = $_POST['password'];
            }

            // Handle image upload
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'img/';
                $file_extension = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
                $new_filename = $employee_id . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_path)) {
                    $employee->getElementsByTagName('image')->item(0)->nodeValue = $upload_path;
                }
            }

            $xml->save('cict.xml');
            header('Location: admin_dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee - Admin</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header {
            background-color: #2c3e50;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .back-btn {
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .back-btn:hover {
            background-color: #2980b9;
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
        }
        .current-image {
            margin-bottom: 10px;
        }
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            font-size: 16px;
        }
        .toggle-password:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="admin_dashboard.php" class="back-btn">Back</a>
        <a href="admin_logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="form-container">
        <h1 style="text-align: center; margin-bottom: 30px; color: #2c3e50;">Edit Employee Information</h1>
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
                <label for="password">Password:</label>
                <div class="password-container">
                    <input type="password" name="password" id="password" value="<?php echo htmlspecialchars($employee_data['password']); ?>" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <span class="toggle-icon">👁️</span>
                    </button>
                </div>
                <small style="color: #666; margin-top: 5px; display: block;">Click the eye icon to show/hide password</small>
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

            <button type="submit" name="submit" class="submit-btn">Update Employee Information</button>
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

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '👁️‍🗨️';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }
    </script>
</body>
</html> 