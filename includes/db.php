<?php
// Database configuration
$servername = "127.0.0.1"; // Or "localhost"
$username = "root";
$password = "";
$dbname = "blood_bank_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>