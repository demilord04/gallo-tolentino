<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['employee_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $errors = [];

    // Validate passwords
    if (empty($password) || empty($confirm_password)) {
        $errors[] = "Both password fields are required.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        try {
            // Load XML file
            $xml = new DOMDocument();
            $xml->preserveWhiteSpace = false;
            $xml->formatOutput = true;
            
            if (!$xml->load('cict.xml')) {
                throw new Exception("Failed to load XML file");
            }
            
            $x = $xml->getElementsByTagName('employees')->item(0);
            if (!$x) {
                throw new Exception("Invalid XML structure");
            }
            
            $employees = $x->getElementsByTagName('employee');
            $employeeFound = false;
            
            foreach ($employees as $employee) {
                if ($employee->getElementsByTagName('id')->item(0)->nodeValue == $id) {
                    $employeeFound = true;
                    $stored_password = $employee->getElementsByTagName('password')->item(0)->nodeValue;
                    
                    // Compare passwords directly
                    if ($password === $stored_password) {
                        // Get image path before removing the node
                        $imagePath = $employee->getElementsByTagName('image')->item(0)->nodeValue;
                        
                        // Remove the employee node
                        $x->removeChild($employee);
                        
                        // Save the XML file
                        if (!$xml->save('cict.xml')) {
                            throw new Exception("Failed to save XML file");
                        }
                        
                        // Delete the profile image if it exists and is not the default avatar
                        if (!empty($imagePath) && file_exists($imagePath) && $imagePath !== 'img/default-avatar.png') {
                            unlink($imagePath);
                        }
                        
                        // Clear session
                        session_destroy();
                        
                        // Show success message and redirect
                        echo "<script>
                            alert('Account successfully deleted!');
                            window.location.href='index.php';
                        </script>";
                        exit;
                    } else {
                        $errors[] = "Incorrect password.";
                    }
            break;
                }
            }
            
            if (!$employeeFound) {
                $errors[] = "Employee not found.";
            }
            
        } catch (Exception $e) {
            $errors[] = "Error: " . $e->getMessage();
            error_log("Delete account error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .delete-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .delete-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .delete-header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .warning-message {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #ffeeba;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: #3498db;
            outline: none;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .error-message {
            color: #dc3545;
            text-align: center;
            margin-bottom: 20px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #3498db;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .checkbox-group {
            margin: 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
        .checkbox-group label {
            margin: 0;
            font-weight: normal;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="delete-container">
        <div class="delete-header">
            <h1>Delete Account</h1>
        </div>
        <div class="warning-message">
            ⚠️ Warning: This action cannot be undone. All your data will be permanently deleted.
        </div>
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="POST" onsubmit="return validateForm()">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="confirm_delete" required>
                <label for="confirm_delete">I understand that this action is permanent and cannot be undone</label>
            </div>
            <button type="submit" class="delete-btn">Delete Account</button>
        </form>
        <div class="back-link">
            <a href="dashboard.php">Back to Home</a>
        </div>
    </div>

    <script>
        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const confirmCheckbox = document.getElementById('confirm_delete').checked;

            if (!password || !confirmPassword) {
                alert('Please fill in both password fields');
                return false;
            }

            if (password !== confirmPassword) {
                alert('Passwords do not match');
                return false;
            }

            if (!confirmCheckbox) {
                alert('Please confirm that you understand this action is permanent');
                return false;
            }

            return confirm("Are you absolutely sure you want to delete your account? This action cannot be undone.");
        }
    </script>
</body>
</html>