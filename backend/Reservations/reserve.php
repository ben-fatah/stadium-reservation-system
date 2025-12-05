<?php
include '../config/db.php';
include '../Authentication/auth.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $slot_id = $_POST['slot_id'];
    $user_id = $_SESSION['user_id'];

    // Check if slot is available
    $stmt = $conn->prepare("SELECT status FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $slot_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $slot = $result->fetch_assoc();

    if($slot['status'] != 'available'){
        echo json_encode(["status"=>"error","message"=>"Slot not available"]);
        exit();
    }

    // Update booking
    $stmt = $conn->prepare("UPDATE bookings SET user_id = ?, status = 'reserved' WHERE id = ?");
    $stmt->bind_param("ii", $user_id, $slot_id);

    if($stmt->execute()){
        echo json_encode(["status"=>"success","message"=>"Slot reserved successfully"]);
    } else {
        echo json_encode(["status"=>"error","message"=>$stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>
