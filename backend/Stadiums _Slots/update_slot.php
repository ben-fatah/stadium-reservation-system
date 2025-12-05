<?php
include '../config/db.php';
include '../Authentication/auth.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $slot_id = $_POST['slot_id'];
    $status = $_POST['status']; // available or reserved

    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $slot_id);

    if($stmt->execute()){
        echo json_encode(["status"=>"success","message"=>"Slot updated"]);
    } else {
        echo json_encode(["status"=>"error","message"=>$stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>
