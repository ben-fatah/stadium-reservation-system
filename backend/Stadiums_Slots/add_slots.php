<?php
header('Content-Type: application/json');
session_start();

include '../../config/database.php';
include '../Authentication/auth.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stadium_id = $_POST['stadium_id'] ?? null;
    $date = $_POST['date'] ?? null;
    $time_slot = $_POST['time_slot'] ?? null;

    if (!$stadium_id || !$date || !$time_slot) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    $status = 'available'; 

    $stmt = $conn->prepare("INSERT INTO bookings (stadium_id, user_id, date, time_slot, status) VALUES (?, NULL, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Database prepare error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("isss", $stadium_id, $date, $time_slot, $status);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Slot added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add slot: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>
