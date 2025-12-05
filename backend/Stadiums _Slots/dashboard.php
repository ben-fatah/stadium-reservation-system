<?php
include '../config/db.php';
include '../Authentication/auth.php';

if($_SESSION['role'] == 'owner'){
    $owner_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("
        SELECT s.id, s.name,
               COUNT(b.id) AS total_slots,
               SUM(b.status='reserved') AS reserved_slots
        FROM stadiums s
        LEFT JOIN bookings b ON s.id = b.stadium_id
        WHERE s.owner_id = ?
        GROUP BY s.id
    ");
    $stmt->bind_param("i", $owner_id);
} else {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("
        SELECT b.id, s.name AS stadium_name, b.date, b.time_slot, b.status
        FROM bookings b
        JOIN stadiums s ON b.stadium_id = s.id
        WHERE b.user_id = ?
        ORDER BY b.date ASC
    ");
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);

$stmt->close();
$conn->close();
?>
