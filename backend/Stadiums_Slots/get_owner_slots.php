<?php
header('Content-Type: application/json');
session_start();

include '../../config/database.php';
include '../Authentication/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$owner_id = $_SESSION['user_id'];

// Get all slots for this owner's stadiums with reservation details
$stmt = $conn->prepare("
    SELECT 
        b.id,
        s.name AS stadium_name,
        b.date,
        b.time_slot,
        b.status,
        u.username AS reserved_by
    FROM bookings b
    JOIN stadiums s ON b.stadium_id = s.id
    LEFT JOIN users u ON b.user_id = u.id
    WHERE s.owner_id = ?
    ORDER BY b.date ASC, b.time_slot ASC
");

$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();

$slots = [];
while ($row = $result->fetch_assoc()) {
    $slots[] = $row;
}

echo json_encode([
    "status" => "success",
    "slots" => $slots
]);

$stmt->close();
$conn->close();
?>