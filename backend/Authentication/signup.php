<?php
include '../config/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; // 'user' or 'owner'

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if($stmt->execute()){
        echo json_encode(["status"=>"success", "message"=>"User registered successfully"]);
    } else {
        echo json_encode(["status"=>"error", "message"=>$stmt->error]);
    }

    $stmt->close();
}
$conn->close();
?>
