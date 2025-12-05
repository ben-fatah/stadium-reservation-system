<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Check if it's an AJAX/fetch request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    } else {
        // Redirect normal users
        header("Location: ../frontend/index.html");
    }
    exit();
}
?>
