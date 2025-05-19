<?php
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['employee_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login</title>
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
        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .login-header h1 a {
            color: inherit;
            text-decoration: none;
            cursor: default;
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
        .login-btn {
            background-color: #3498db;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-btn:hover {
            background-color: #2980b9;
        }
        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 20px;
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
        .view-employees-btn {
            display: inline-block;
            background-color: #3498db;
            color: white !important;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none !important;
            font-size: 14px;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        .view-employees-btn:hover {
            background-color: #2980b9;
            text-decoration: none !important;
            color: white !important;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><a href="admin_login.php">Employee Login</a></h1>
        </div>
        <div class="error-message" id="errorMessage"></div>
        <form method="POST" action="verify_login.php">
            <div class="form-group">
                <label for="employee_id">Employee ID:</label>
                <input type="text" id="employee_id" name="employee_id" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
        <div class="login-link">
            <p>Don't have an account? <a href="add.php">Sign up here</a></p>
            <a href="list.php" class="view-employees-btn">Employee List</a>
        </div>
    </div>

    <script>
        // Check if there's an error message in the URL
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        if (error) {
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.style.display = 'block';
            errorMessage.textContent = 'Invalid employee ID or password';
                }

        // Add click counter for admin login
        let clickCount = 0;
        const titleLink = document.querySelector('.login-header h1 a');
        titleLink.addEventListener('click', function(e) {
            e.preventDefault();
            clickCount++;
            if (clickCount >= 5) {
                window.location.href = 'admin_login.php';
            }
        });
    </script>
</body>
</html>