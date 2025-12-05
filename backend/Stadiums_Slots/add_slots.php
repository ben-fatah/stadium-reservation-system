<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../../config/database.php';
include '../Authentication/auth.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stadium_id = $_POST['stadium_id'] ?? null;
    $date = $_POST['date'] ?? null;
    $time_slot = $_POST['time_slot'] ?? null;
    $status = 'available'; // default status

    if (!$stadium_id || !$date || !$time_slot) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO bookings (stadium_id, user_id, date, time_slot, status) VALUES (?, NULL, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("isss", $stadium_id, $date, $time_slot, $status);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Slot added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Execute failed: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>
