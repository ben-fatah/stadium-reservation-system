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
        echo json_encode(["status" => "error", "message" => "Slot ID required"]);
        exit;
    }

    // Verify this reservation belongs to the user
    $stmt = $conn->prepare("SELECT user_id, status FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $slot_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    if (!$booking) {
        echo json_encode(["status" => "error", "message" => "Booking not found"]);
        exit;
    }

    if ($booking['user_id'] != $user_id) {
        echo json_encode(["status" => "error", "message" => "This is not your reservation"]);
        exit;
    }

    if ($booking['status'] !== 'reserved') {
        echo json_encode(["status" => "error", "message" => "This slot is not reserved"]);
        exit;
    }

    // Cancel the reservation: set user_id to NULL and status to available
    $stmt = $conn->prepare("UPDATE bookings SET user_id = NULL, status = 'available' WHERE id = ?");
    $stmt->bind_param("i", $slot_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Reservation cancelled successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to cancel: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

$conn->close();
?>