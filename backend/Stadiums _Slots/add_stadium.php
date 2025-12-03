<?php
include '../config/db.php';
include '../Authentication/auth.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $owner_id = $_SESSION['user_id']; // use session for owner
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $photo = $_POST['photo']; // can be a path or URL

    $stmt = $conn->prepare("INSERT INTO stadiums (owner_id, name, location, description, photo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $owner_id, $name, $location, $description, $photo);

    if($stmt->execute()){
        echo json_encode(["status"=>"success","message"=>"Stadium added successfully"]);
    } else {
        echo json_encode(["status"=>"error","message"=>$stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>
