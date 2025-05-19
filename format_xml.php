<?php
// Load and format the XML file
$xml = new DOMDocument;
$xml->load('cict.xml');

// Enable formatting
$xml->formatOutput = true;
$xml->preserveWhiteSpace = false;

// Save the formatted XML
$xml->save('cict.xml');

echo "XML file has been reformatted successfully!";
?> 