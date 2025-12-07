<?php
// Unified database configuration file
$host = 'localhost';
$db   = 'stadium_db';
$user = 'stadium_user';  // Change to 'root' if using root
$pass = 'password123';    // Change to your actual password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    // Check if the request is from JavaScript/fetch
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "Database connection failed: " . $conn->connect_error
        ]);
    } else {
        echo "Database connection failed: " . $conn->connect_error;
    }
    exit();
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>