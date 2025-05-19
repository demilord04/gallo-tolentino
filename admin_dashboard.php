<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

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
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .dashboard-container {
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
        .logout-btn, .session-btn {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .session-btn {
            background-color: #2980b9;
        }
        .session-btn:hover {
            background-color: #2471a3;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
        .employee-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .employee-table th {
            background-color: #2980b9;
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
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            font-size: 12px;
            margin-right: 5px;
            cursor: pointer;
            display: inline-block;
            height: 15px;
            width: 50px;
            text-align: center;
        }
        .view-btn {
            background-color: #2ecc71;
        }
        .view-btn:hover {
            background-color: #27ae60;
        }
        .update-btn {
            background-color: #f1c40f;
        }
        .update-btn:hover {
            background-color: #f39c12;
        }
        .delete-btn {
            background-color: #e74c3c;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .no-records {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: space-between;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .modal-content {
            position: relative;
            background-color: white;
            margin: 50px auto;
            padding: 20px;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            font-weight: bold;
            color: #666;
            cursor: pointer;
            transition: color 0.3s;
        }
        .close-btn:hover {
            color: #000;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <a href="session_history.php" class="session-btn">Session History</a>
            <h1>Admin Dashboard</h1>
            <a href="admin_logout.php" class="logout-btn">Logout</a>
        </div>
         
        <table class="employee-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th></th>
                    <th>Name</th>
                    <th style="width: 200px;"></th>
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
                        
                        // Get all other details
                        $ageNode = $employee->getElementsByTagName('age')->item(0);
                        $genderNode = $employee->getElementsByTagName('gender')->item(0);
                        $positionNode = $employee->getElementsByTagName('position')->item(0);
                        $departmentNode = $employee->getElementsByTagName('department')->item(0);
                        $emailNode = $employee->getElementsByTagName('email')->item(0);
                        $phoneNode = $employee->getElementsByTagName('phone')->item(0);
                        $addressNode = $employee->getElementsByTagName('address')->item(0);
                        $emergencyContactNode = $employee->getElementsByTagName('emergency_contact')->item(0);
                        
                        $age = $ageNode ? $ageNode->nodeValue : 'N/A';
                        $gender = $genderNode ? $genderNode->nodeValue : 'N/A';
                        $position = $positionNode ? $positionNode->nodeValue : 'N/A';
                        $department = $departmentNode ? $departmentNode->nodeValue : 'N/A';
                        $email = $emailNode ? $emailNode->nodeValue : 'N/A';
                        $phone = $phoneNode ? $phoneNode->nodeValue : 'N/A';
                        $address = $addressNode ? $addressNode->nodeValue : 'N/A';
                        $emergencyContact = $emergencyContactNode ? $emergencyContactNode->nodeValue : 'N/A';
                        
                        // Get Image
                        $imageNode = $employee->getElementsByTagName('image')->item(0);
                        $image = $imageNode ? $imageNode->nodeValue : 'img/default-avatar.png';
                        
                        // Ensure image path exists, otherwise use default
                        if (!file_exists($image)) {
                            $image = 'img/default-avatar.png';
                        }
                        
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($id) . "</td>";
                        echo "<td><img src='" . htmlspecialchars($image) . "' alt='Employee Photo' class='employee-image'></td>";
                        echo "<td>" . htmlspecialchars($name) . "</td>";
                        echo "<td>";
                        echo "<div class='action-buttons'>";
                        echo "<a href='#' class='action-btn view-btn' onclick='openModal(\"" . htmlspecialchars($id) . "\", \"" . htmlspecialchars($name) . "\", \"" . htmlspecialchars($image) . "\", \"" . htmlspecialchars($age) . "\", \"" . htmlspecialchars($gender) . "\", \"" . htmlspecialchars($position) . "\", \"" . htmlspecialchars($department) . "\", \"" . htmlspecialchars($email) . "\", \"" . htmlspecialchars($phone) . "\", \"" . htmlspecialchars($address) . "\", \"" . htmlspecialchars($emergencyContact) . "\")'>View</a>";
                        echo "<a href='admin_update.php?id=" . htmlspecialchars($id) . "' class='action-btn update-btn'>Update</a>";
                        echo "<a href='#' class='action-btn delete-btn' onclick='confirmDelete(\"" . htmlspecialchars($id) . "\", \"" . htmlspecialchars($name) . "\")'>Delete</a>";
                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='no-records'>No employees found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <div id="modalContent">
                <!-- Content will be dynamically inserted here -->
            </div>
        </div>
    </div>

    <script>
        function openModal(id, name, image, age, gender, position, department, email, phone, address, emergencyContact) {
            const modal = document.getElementById('viewModal');
            const modalContent = document.getElementById('modalContent');
            
            // Create content for the modal
            modalContent.innerHTML = `
                <h2>Employee Details</h2>
                <div style="text-align: center; margin: 20px 0;">
                    <img src="${image}" alt="Employee Photo" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
                </div>
                <div style="margin: 20px 0;">
                    <p><strong>ID:</strong> ${id}</p>
                    <p><strong>Name:</strong> ${name}</p>
                    <p><strong>Age:</strong> ${age}</p>
                    <p><strong>Gender:</strong> ${gender}</p>
                    <p><strong>Position:</strong> ${position}</p>
                    <p><strong>Department:</strong> ${department}</p>
                    <p><strong>Email:</strong> ${email}</p>
                    <p><strong>Phone:</strong> ${phone}</p>
                    <p><strong>Address:</strong> ${address}</p>
                    <p><strong>Emergency Contact:</strong> ${emergencyContact}</p>
                </div>
            `;
            
            modal.style.display = 'block';
        }

        function closeModal() {
            const modal = document.getElementById('viewModal');
            modal.style.display = 'none';
        }

        function confirmDelete(id, name) {
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = 'admin_delete.php?id=' + id;
            }
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('viewModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html> 