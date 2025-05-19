<?php
date_default_timezone_set('Asia/Manila');
function recordSession($employee_id, $action) {
    $xml = new DOMDocument();
    $xml->preserveWhiteSpace = false;
    $xml->formatOutput = true;
    
    // Load existing session history
    if (file_exists('session_history.xml')) {
        $xml->load('session_history.xml');
    } else {
        // Create new XML structure if file doesn't exist
        $xml->loadXML('<?xml version="1.0" encoding="UTF-8"?><sessions></sessions>');
    }
    
    // Get the root element
    $root = $xml->getElementsByTagName('sessions')->item(0);
    
    // Create new session entry
    $session = $xml->createElement('session');
    
    // Add session details
    $id = $xml->createElement('employee_id', $employee_id);
    $actionElement = $xml->createElement('action', $action);
    $timestamp = $xml->createElement('timestamp', date('Y-m-d H:i:s'));
    
    // Append elements to session
    $session->appendChild($id);
    $session->appendChild($actionElement);
    $session->appendChild($timestamp);
    
    // Append session to root
    $root->appendChild($session);
    
    // Save the XML file
    $xml->save('session_history.xml');
}
?> 