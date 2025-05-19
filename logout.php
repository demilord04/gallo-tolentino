<?php
session_start();
require_once 'session_tracker.php';

if (isset($_SESSION['employee_id'])) {
    recordSession($_SESSION['employee_id'], 'Logout');
}

session_destroy();
header('Location: index.php');
exit;
?> 