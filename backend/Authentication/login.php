<?php
session_start();
include '../config/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($user = $result->fetch_assoc()){
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            echo json_encode(["status"=>"success", "role"=>$user['role']]);
        } else {
            echo json_encode(["status"=>"error", "message"=>"Invalid password"]);
        }
    } else {
        echo json_encode(["status"=>"error", "message"=>"User not found"]);
    }

    $stmt->close();
}
$conn->close();
?>
