<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Load employee data for lookup
$employee_data = [];
$emp_xml = new DOMDocument();
$emp_xml->preserveWhiteSpace = false;
$emp_xml->formatOutput = true;
$emp_xml->load('cict.xml');
$employees = $emp_xml->getElementsByTagName('employee');
foreach ($employees as $employee) {
    $id = $employee->getElementsByTagName('id')->item(0)->nodeValue;
    $firstname = $employee->getElementsByTagName('firstname')->item(0)->nodeValue;
    $lastname = $employee->getElementsByTagName('lastname')->item(0)->nodeValue;
    $name = trim($firstname . ' ' . $lastname);
    $imageNode = $employee->getElementsByTagName('image')->item(0);
    $image = $imageNode ? $imageNode->nodeValue : 'img/default-avatar.png';
    if (!file_exists($image)) {
        $image = 'img/default-avatar.png';
    }
    $employee_data[$id] = [
        'name' => $name,
        'image' => $image
    ];
}

// Load session history
$sessions = [];
if (file_exists('session_history.xml')) {
    $sess_xml = new DOMDocument();
    $sess_xml->preserveWhiteSpace = false;
    $sess_xml->formatOutput = true;
    $sess_xml->load('session_history.xml');
    $session_nodes = $sess_xml->getElementsByTagName('session');
    foreach ($session_nodes as $session) {
        $employee_id = $session->getElementsByTagName('employee_id')->item(0)->nodeValue;
        $action = $session->getElementsByTagName('action')->item(0)->nodeValue;
        $timestamp = $session->getElementsByTagName('timestamp')->item(0)->nodeValue;
        $sessions[] = [
            'employee_id' => $employee_id,
            'action' => $action,
            'timestamp' => $timestamp
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session History</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            position: relative;
        }
        h1 {
            color: #2c3e50;
            margin: 0;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        .back-btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .back-btn:hover {
            background-color: #2980b9;
        }
        .logout-btn {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .history-table th {
            background-color: #2980b9;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .history-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .history-table tr:hover {
            background-color: #f5f5f5;
        }
        .employee-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .action-login {
            color: #2ecc71;
            font-weight: bold;
        }
        .action-logout {
            color: #e74c3c;
            font-weight: bold;
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
        <div class="header">
            <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
            <h1>Session History</h1>
            <a href="admin_logout.php" class="logout-btn">Logout</a>
        </div>

        <table class="history-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Date & Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($sessions) > 0) {
                    foreach ($sessions as $entry) {
                        $id = $entry['employee_id'];
                        $action = $entry['action'];
                        $timestamp = $entry['timestamp'];
                        $name = isset($employee_data[$id]) ? $employee_data[$id]['name'] : 'Unknown';
                        $image = isset($employee_data[$id]) ? $employee_data[$id]['image'] : 'img/default-avatar.png';
                        $action_class = strtolower($action) === 'login' ? 'action-login' : 'action-logout';
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($id) . "</td>";
                        echo "<td><img src='" . htmlspecialchars($image) . "' alt='Employee Photo' class='employee-image'></td>";
                        echo "<td>" . htmlspecialchars($name) . "</td>";
                        echo "<td>" . htmlspecialchars($timestamp) . "</td>";
                        echo "<td class='" . $action_class . "'>" . htmlspecialchars($action) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='no-records'>No session history found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html> 