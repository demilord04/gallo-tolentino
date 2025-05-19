<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Check if password was provided
if (!isset($_POST['verify_password'])) {
    echo json_encode(['success' => false, 'message' => 'No password provided']);
    exit;
}

$password = $_POST['verify_password'];

// Load XML file
$xml = new DOMDocument;
$xml->load('cict.xml');
$x = $xml->getElementsByTagName('employees')->item(0);
$employees = $x->getElementsByTagName('employee');

// Find current employee
$current_employee = null;
foreach ($employees as $employee) {
    if ($employee->getElementsByTagName('id')->item(0)->nodeValue == $_SESSION['employee_id']) {
        $current_employee = $employee;
        break;
    }
}

if ($current_employee) {
    $stored_password = $current_employee->getElementsByTagName('password')->item(0)->nodeValue;
    
    // Verify password
    if ($password === $stored_password) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Employee not found']);
}
?> 