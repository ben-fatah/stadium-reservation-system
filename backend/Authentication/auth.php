<?php
session_start();

// Redirect if not logged in
if(!isset($_SESSION['user_id'])){
    header("Location: ../frontend/index.html");
    exit();
}
?>
