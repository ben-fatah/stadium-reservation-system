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

$owner_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, name, location, description, photo FROM stadiums WHERE owner_id = ?");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $owner_id);
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
