<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load XML file
$xml = new DOMDocument();
$xml->preserveWhiteSpace = false;
$xml->formatOutput = true;

if (file_exists('cict.xml')) {
    $xml->load('cict.xml');
} else {
    die("No employee records found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .login-btn {
            position: fixed;
            top: 40px;
            right: 130px;
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .login-btn:hover {
            background-color: #2980b9;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .employee-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .employee-table th {
            background-color: #2c3e50;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .employee-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .employee-table tr:hover {
            background-color: #f5f5f5;
        }
        .employee-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .no-records {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Employee List</h1>
        <a href="index.php" class="login-btn">Log In</a>
        
        <table class="employee-table">
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Position</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $employees = $xml->getElementsByTagName('employee');
                if ($employees->length > 0) {
                    foreach ($employees as $employee) {
                        // Get ID
                        $idNode = $employee->getElementsByTagName('id')->item(0);
                        $id = $idNode ? $idNode->nodeValue : 'N/A';
                        
                        // Get Name (combine firstname and lastname)
                        $firstnameNode = $employee->getElementsByTagName('firstname')->item(0);
                        $lastnameNode = $employee->getElementsByTagName('lastname')->item(0);
                        $firstname = $firstnameNode ? $firstnameNode->nodeValue : '';
                        $lastname = $lastnameNode ? $lastnameNode->nodeValue : '';
                        $name = trim($firstname . ' ' . $lastname);
                        
                        // Get Position
                        $positionNode = $employee->getElementsByTagName('position')->item(0);
                        $position = $positionNode ? $positionNode->nodeValue : 'N/A';
                        
                        // Get Image
                        $imageNode = $employee->getElementsByTagName('image')->item(0);
                        $image = $imageNode ? $imageNode->nodeValue : 'img/default-avatar.png';
                        
                        // Ensure image path exists, otherwise use default
                        if (!file_exists($image)) {
                            $image = 'img/default-avatar.png';
                        }
                        
                        echo "<tr>";
                        echo "<td><img src='" . htmlspecialchars($image) . "' alt='Employee Photo' class='employee-image'></td>";
                        echo "<td>" . htmlspecialchars($id) . "</td>";
                        echo "<td>" . htmlspecialchars($name) . "</td>";
                        echo "<td>" . htmlspecialchars($position) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='no-records'>No employees found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html> 