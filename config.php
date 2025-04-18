<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'church_db';

// Set default timezone
date_default_timezone_set('Asia/Kolkata');

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sync MySQL timezone (corrected syntax)
$conn->query("SET time_zone = '+05:30'"); 
?>
