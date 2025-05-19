<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Load and parse the XML file
    $xml = simplexml_load_file('cictadmin.xml');
    
    if ($xml) {
        $stored_username = (string)$xml->credentials->username;
        $stored_password = (string)$xml->credentials->password;

        // Verify credentials
        if ($username === $stored_username && $password === $stored_password) {
            $_SESSION['admin_id'] = 1; // Set admin session
            header('Location: admin_dashboard.php');
            exit;
        }
    }
    
    // If login fails, redirect back with error
    header('Location: admin_login.php?error=1');
    exit;
}
?> 