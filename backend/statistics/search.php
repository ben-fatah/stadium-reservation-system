<?php
include '../config/db.php';
include '../Authentication/auth.php';

$location = $_GET['location'] ?? '';
$date = $_GET['date'] ?? '';

$query = "SELECT s.*, b.id AS slot_id, b.date, b.time_slot, b.status 
          FROM stadiums s
          LEFT JOIN bookings b ON s.id = b.stadium_id
          WHERE 1=1";

$params = [];
$types = "";

if($location){
    $query .= " AND s.location LIKE ?";
    $params[] = "%$location%";
    $types .= "s";
}
if($date){
    $query .= " AND b.date = ?";
    $params[] = $date;
    $types .= "s";
}

$stmt = $conn->prepare($query);

if($params){
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$stadiums = [];
while($row = $result->fetch_assoc()){
    $stadiums[] = $row;
}

echo json_encode($stadiums);

$stmt->close();
$conn->close();
?>
