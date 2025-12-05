<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../../config/database.php';
include '../Authentication/auth.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

$slot_id = $_POST['slot_id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$slot_id || !$status) {
    echo json_encode(["status" => "error", "message" => "Missing parameters"]);
    exit;
}

$stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("si", $status, $slot_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Slot updated"]);
} else {
    echo json_encode(["status" => "error", "message" => "Execute failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
