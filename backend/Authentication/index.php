<?php
session_start();

if(isset($_SESSION['user_id'])){
    if($_SESSION['role'] == 'owner'){
        header("Location: ../frontend/home_owner.html");
    } else {
        header("Location: ../frontend/home_user.html");
    }
    exit();
} else {
    header("Location: ../frontend/index.html");
}
?>
