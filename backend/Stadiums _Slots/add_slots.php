<?php
include '../config/db.php';
include '../Authentication/auth.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $stadium_id = $_POST['stadium_id'];
    $date = $_POST['date'];
    $time_slot = $_POST['time_slot'];

    $stmt = $conn->prepare("INSERT INTO bookings (stadium_id, date, time_slot) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $stadium_id, $date, $time_slot);

    if($stmt->execute()){
        echo json_encode(["status"=>"success","message"=>"Slot added successfully"]);
    } else {
        echo json_encode(["status"=>"error","message"=>$stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>
