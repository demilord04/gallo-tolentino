<?php
session_start();

// Function to handle image paths
function getEmployeeImage($image_path) {
    // Check if image exists in covers directory
    if (!empty($image_path) && file_exists($image_path)) {
        return $image_path;
    }
    
    // Check if image exists in img/employees directory
    $employee_img = 'img/employees/' . basename($image_path);
    if (file_exists($employee_img)) {
        return $employee_img;
    }
    
    // Return default avatar
    return 'img/default-avatar.png';
}

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['employee_name'])) {
    header('Location: index.php');
    exit;
}

$logged_in_id = $_SESSION['employee_id'];

// Load and validate XML
$xml = new DOMDocument;
if (!file_exists('cict.xml')) {
    die('Error: XML file not found');
}

try {
    $xml->load('cict.xml');
} catch (Exception $e) {
    die('Error loading XML file: ' . $e->getMessage());
}

$x = $xml->getElementsByTagName('employees')->item(0);
if (!$x) {
    die('Error: Invalid XML structure');
}

$employees = $x->getElementsByTagName('employee');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
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
        .user-info {
            color: white;
            display: flex;
            align-items: center;
            gap: 20px;
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
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }
        .employee-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            text-align: center;
        }
        .employee-card img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #3498db;
            margin-bottom: 20px;
        }
        .employee-info {
            text-align: left;
            margin-top: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }
        .info-label {
            width: 150px;
            font-weight: 600;
            color: #2c3e50;
        }
        .info-value {
            flex: 1;
        }
        .welcome-message {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .employee-image-container {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 20px;
        }
        .employee-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['employee_name']); ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-message">
            <h1>Welcome to Your Dashboard</h1>
            <p>Here you can view and manage your employee information</p>
        </div>

        <?php
        foreach($employees as $employee) {
            if($employee->getElementsByTagName('id')->item(0)->nodeValue == $logged_in_id) {
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
                <div class="employee-card">
                    <?php
                    $image_path = getEmployeeImage($image);
                    ?>
                    <div class="employee-image-container">
                        <img src="<?php echo htmlspecialchars($image_path); ?>" 
                             alt="Employee Photo" 
                             class="employee-image"
                             onerror="this.onerror=null; this.src='img/default-avatar.png';">
                    </div>
                    <div class="employee-info">
                        <div class="info-row">
                            <div class="info-label">Employee ID:</div>
                            <div class="info-value"><?php echo $logged_in_id; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Name:</div>
                            <div class="info-value"><?php echo $firstname . ' ' . $lastname; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Age:</div>
                            <div class="info-value"><?php echo $age; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Gender:</div>
                            <div class="info-value"><?php echo $gender; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Position:</div>
                            <div class="info-value"><?php echo $position; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Department:</div>
                            <div class="info-value"><?php echo $department; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Email:</div>
                            <div class="info-value"><?php echo $email; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Phone:</div>
                            <div class="info-value"><?php echo $phone; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Address:</div>
                            <div class="info-value"><?php echo $address; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Emergency Contact:</div>
                            <div class="info-value"><?php echo $emergency_contact; ?></div>
                        </div>
                    </div>
                </div>
                <?php
                break;
            }
        }
        ?>
    </div>
</body>
</html> 