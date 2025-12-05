<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include '../../config/database.php';  
include '../Authentication/auth.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

// Validate stadium_id
if (!isset($_GET['stadium_id']) || empty($_GET['stadium_id'])) {
    echo json_encode(["status" => "error", "message" => "stadium_id is required"]);
    exit;
}

$stadium_id = intval($_GET['stadium_id']);

$stmt = $conn->prepare("SELECT id, date, time_slot, status FROM bookings WHERE stadium_id = ?");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $stadium_id);
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
