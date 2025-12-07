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

    // Verify stadium belongs to this owner
    $owner_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id FROM stadiums WHERE id = ? AND owner_id = ?");
    $stmt->bind_param("ii", $stadium_id, $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Stadium not found or you don't own it"]);
        exit;
    }
    $stmt->close();

    // Validate date is within 7 days from today
    $today = new DateTime();
    $slot_date = new DateTime($date);
    $max_date = (clone $today)->modify('+7 days');
    
    if ($slot_date < $today) {
        echo json_encode(["status" => "error", "message" => "Cannot add slots for past dates"]);
        exit;
    }
    
    if ($slot_date > $max_date) {
        echo json_encode(["status" => "error", "message" => "Can only add slots for the next 7 days"]);
        exit;
    }

    // Check if slot already exists
    $stmt = $conn->prepare("SELECT id FROM bookings WHERE stadium_id = ? AND date = ? AND time_slot = ?");
    $stmt->bind_param("iss", $stadium_id, $date, $time_slot);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "This time slot already exists for this date"]);
        exit;
    }
    $stmt->close();

    // Insert new slot
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