<?php 
header('Content-Type: application/json'); 
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);

include '../../config/database.php';  // changed path to ../../config/database.php

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    $role = $_POST['role'] ?? '';

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)"); 
    $stmt->bind_param("ssss", $username, $email, $password, $role); 

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User registered successfully"]); 
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]); 
    }

    $stmt->close(); 
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close(); 
?>
