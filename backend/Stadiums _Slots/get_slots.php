<?php
include '../config/db.php';
include '../Authentication/auth.php';

$stadium_id = $_GET['stadium_id'];

$stmt = $conn->prepare("SELECT * FROM bookings WHERE stadium_id = ?");
$stmt->bind_param("i", $stadium_id);
$stmt->execute();
$result = $stmt->get_result();

$slots = [];
while($row = $result->fetch_assoc()){
    $slots[] = $row;
}

echo json_encode($slots);

$stmt->close();
$conn->close();
?>
