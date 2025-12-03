<?php
include '../config/db.php';
include '../Authentication/auth.php';

if($_SESSION['role'] != 'owner'){
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit();
}

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
$stmt->execute();
$result = $stmt->get_result();

$stats = [];
while($row = $result->fetch_assoc()){
    $stats[] = $row;
}

echo json_encode($stats);

$stmt->close();
$conn->close();
?>
