<?php
include '../config/db.php';
include '../Authentication/auth.php';

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

if($role == 'owner'){
    // Get stadiums for this owner
    $stmt = $conn->prepare("SELECT * FROM stadiums WHERE owner_id = ?");
    $stmt->bind_param("i", $user_id);
} else {
    // Get all stadiums for users
    $stmt = $conn->prepare("SELECT * FROM stadiums");
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
