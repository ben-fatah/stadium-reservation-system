<?php
include '../config/db.php';

// يمكنك تحديد owner_id من session لاحقاً
$owner_id = $_GET['owner_id'];

$stmt = $conn->prepare("SELECT * FROM stadiums WHERE owner_id = ?");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();

$stadiums = [];
while($row = $result->fetch_assoc()) {
    $stadiums[] = $row;
}

echo json_encode($stadiums);

$stmt->close();
$conn->close();
?>
