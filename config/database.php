<?php
// Database connection settings
$host = 'localhost';       // Server, usually localhost
$db   = 'stadium_db';      // Database name
$user = 'stadium_user';            // Database username
$pass = 'password123';                // Database password

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// This file can be included in any PHP file that needs DB access
?>
