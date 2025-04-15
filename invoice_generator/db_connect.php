<?php
// Database credentials
$host = 'localhost';
$user = 'root';
$pass = ''; // Default password is empty in XAMPP
$dbname = 'invoice_generator'; // <-- isko apne actual DB name se replace karo

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
