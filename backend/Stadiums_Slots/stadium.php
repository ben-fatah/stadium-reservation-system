<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include '../../config/database.php';
include '../Authentication/auth.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

if ($role === 'owner') {
    $stmt = $conn->prepare("SELECT id, name, location, description, photo FROM stadiums WHERE owner_id = ?");
    $stmt->bind_param("i", $user_id);
} else {
    $stmt = $conn->prepare("SELECT id, name, location, description, photo FROM stadiums");
}

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$stadiums = [];
while ($row = $result->fetch_assoc()) {
    $stadiums[] = $row;
}

echo json_encode([
    "status" => "success",
    "stadiums" => $stadiums
]);

$stmt->close();
$conn->close();
?>
