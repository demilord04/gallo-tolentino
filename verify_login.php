<?php
session_start();
require_once 'session_tracker.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $password = $_POST['password'];

    $xml = new DOMDocument;
    $xml->load('cict.xml');
    $x = $xml->getElementsByTagName('employees')->item(0);
    $employees = $x->getElementsByTagName('employee');

    $authenticated = false;
    $employee_data = null;

    foreach ($employees as $employee) {
        if ($employee->getElementsByTagName('id')->item(0)->nodeValue == $employee_id) {
            $stored_password = $employee->getElementsByTagName('password')->item(0)->nodeValue;
            
            if ($password === $stored_password) {
                $authenticated = true;
                $employee_data = [
                    'id' => $employee->getElementsByTagName('id')->item(0)->nodeValue,
                    'firstname' => $employee->getElementsByTagName('firstname')->item(0)->nodeValue,
                    'lastname' => $employee->getElementsByTagName('lastname')->item(0)->nodeValue,
                    'position' => $employee->getElementsByTagName('position')->item(0)->nodeValue,
                    'department' => $employee->getElementsByTagName('department')->item(0)->nodeValue
                ];
                break;
            }
        }
    }

    if ($authenticated) {
        $_SESSION['employee_id'] = $employee_data['id'];
        $_SESSION['employee_name'] = $employee_data['firstname'] . ' ' . $employee_data['lastname'];
        $_SESSION['employee_position'] = $employee_data['position'];
        $_SESSION['employee_department'] = $employee_data['department'];
        recordSession($employee_data['id'], 'Login');
        header('Location: dashboard.php');
        exit;
    } else {
        header('Location: index.php?error=1');
        exit;
    }
}
?> 