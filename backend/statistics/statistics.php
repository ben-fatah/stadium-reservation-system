<?php
header('Content-Type: application/json; charset=utf-8');
include '../config/db.php';
include '../Authentication/auth.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$owner_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT 
        (SELECT COUNT(*) FROM stadiums WHERE owner_id = ?) AS total_stadiums,
        (SELECT COUNT(*) FROM bookings b JOIN stadiums s ON b.stadium_id = s.id WHERE s.owner_id = ? AND b.status = 'reserved') AS total_reservations
");
$stmt->bind_param("ii", $owner_id, $owner_id);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();
$stmt->close();

echo json_encode([
    "total_stadiums" => (int)$summary['total_stadiums'],
    "total_reservations" => (int)$summary['total_reservations']
]);

$conn->close();
?>
