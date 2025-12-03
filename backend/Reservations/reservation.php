<?php
include '../config/db.php';
include '../Authentication/auth.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT b.id, s.name AS stadium_name, b.date, b.time_slot, b.status
    FROM bookings b
    JOIN stadiums s ON b.stadium_id = s.id
    WHERE b.user_id = ?
    ORDER BY b.date ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
while($row = $result->fetch_assoc()){
    $reservations[] = $row;
}

echo json_encode($reservations);

$stmt->close();
$conn->close();
?>
