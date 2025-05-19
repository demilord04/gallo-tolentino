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

// Load XML file
$xml = new DOMDocument();
$xml->preserveWhiteSpace = false;
$xml->formatOutput = true;
$xml->load('cict.xml');

// Find and remove the employee
$employees = $xml->getElementsByTagName('employee');
$employeeToDelete = null;

foreach ($employees as $employee) {
    if ($employee->getElementsByTagName('id')->item(0)->nodeValue == $employee_id) {
        $employeeToDelete = $employee;
        break;
    }
}

if ($employeeToDelete) {
    // Get the image path before deleting
    $imageNode = $employeeToDelete->getElementsByTagName('image')->item(0);
    $imagePath = $imageNode ? $imageNode->nodeValue : null;

    // Remove the employee node
    $employeeToDelete->parentNode->removeChild($employeeToDelete);

    // Save the XML file
    $xml->save('cict.xml');

    // Delete the employee's image if it exists and is not the default avatar
    if ($imagePath && file_exists($imagePath) && $imagePath !== 'img/default-avatar.png') {
        unlink($imagePath);
    }

    // Delete session history entries for this employee
    if (file_exists('session_history.xml')) {
        $session_xml = new DOMDocument();
        $session_xml->preserveWhiteSpace = false;
        $session_xml->formatOutput = true;
        $session_xml->load('session_history.xml');
        
        $sessions = $session_xml->getElementsByTagName('session');
        $sessionsToRemove = [];
        
        // Find all sessions for this employee
        foreach ($sessions as $session) {
            if ($session->getElementsByTagName('employee_id')->item(0)->nodeValue == $employee_id) {
                $sessionsToRemove[] = $session;
            }
        }
        
        // Remove the sessions
        foreach ($sessionsToRemove as $session) {
            $session->parentNode->removeChild($session);
        }
        
        // Save the updated session history
        $session_xml->save('session_history.xml');
    }
}

// Redirect back to admin dashboard
header('Location: admin_dashboard.php');
exit;
?> 