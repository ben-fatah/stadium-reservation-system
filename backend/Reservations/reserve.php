<?php
header('Content-Type: application/json');
session_start();

include '../../config/database.php';
include '../Authentication/auth.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slot_id = $_POST['slot_id'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$slot_id) {
        echo json_encode(["status" => "error", "message" => "Invalid slot ID"]);
        exit;
    }

    // Check if slot exists and is available
    $stmt = $conn->prepare("SELECT id, status, user_id FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $slot_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $slot = $result->fetch_assoc();

    if (!$slot) {
        echo json_encode(["status" => "error", "message" => "Slot not found"]);
        exit;
    }

    if ($slot['status'] !== 'available') {
        echo json_encode(["status" => "error", "message" => "Slot is already reserved"]);
        exit;
    }

    // Reserve the slot - CRITICAL FIX: Set BOTH user_id AND status
    $stmt = $conn->prepare("UPDATE bookings SET user_id = ?, status = 'reserved' WHERE id = ? AND status = 'available'");
    $stmt->bind_param("ii", $user_id, $slot_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode([
            "status" => "success", 
            "message" => "Slot reserved successfully! 🎉"
        ]);
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "Failed to reserve slot. It may have been taken by someone else."
        ]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>