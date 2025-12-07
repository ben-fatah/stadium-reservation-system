<?php
header('Content-Type: application/json');
session_start();

include '../../config/database.php';
include '../Authentication/auth.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$action = $_GET['action'] ?? '';

switch($action){
    case 'send':
        // Send a message
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $receiver_id = $_POST['receiver_id'];
            $message = $_POST['message'];
            $sender_id = $_SESSION['user_id'];

            $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $sender_id, $receiver_id, $message);

            if($stmt->execute()){
                echo json_encode(["status"=>"success","message"=>"Message sent successfully"]);
            } else {
                echo json_encode(["status"=>"error","message"=>$stmt->error]);
            }

            $stmt->close();
        }
        break;

    case 'inbox':
        // Get received messages
        $user_id = $_SESSION['user_id'];

        $stmt = $conn->prepare("
            SELECT m.id, m.sender_id, u.username AS sender_name, u.role AS sender_role, m.message, m.sent_at
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.receiver_id = ?
            ORDER BY m.sent_at DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $messages = [];
        while($row = $result->fetch_assoc()){
            $messages[] = $row;
        }

        echo json_encode($messages);
        $stmt->close();
        break;

    case 'sent':
        // Get sent messages
        $user_id = $_SESSION['user_id'];

        $stmt = $conn->prepare("
            SELECT m.id, m.receiver_id, u.username AS receiver_name, u.role AS receiver_role, m.message, m.sent_at
            FROM messages m
            JOIN users u ON m.receiver_id = u.id
            WHERE m.sender_id = ?
            ORDER BY m.sent_at DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $messages = [];
        while($row = $result->fetch_assoc()){
            $messages[] = $row;
        }

        echo json_encode($messages);
        $stmt->close();
        break;

    case 'get_users':
        // Get list of users to message (owners see users, users see owners)
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['role'];
        
        if($user_role === 'owner'){
            // Owners can message users who have reservations in their stadiums
            $stmt = $conn->prepare("
                SELECT DISTINCT u.id, u.username, u.role
                FROM users u
                JOIN bookings b ON u.id = b.user_id
                JOIN stadiums s ON b.stadium_id = s.id
                WHERE s.owner_id = ? AND u.id != ?
                ORDER BY u.username ASC
            ");
            $stmt->bind_param("ii", $user_id, $user_id);
        } else {
            // Users can message stadium owners
            $stmt = $conn->prepare("
                SELECT DISTINCT u.id, u.username, u.role
                FROM users u
                JOIN stadiums s ON u.id = s.owner_id
                WHERE u.id != ?
                ORDER BY u.username ASC
            ");
            $stmt->bind_param("i", $user_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while($row = $result->fetch_assoc()){
            $users[] = $row;
        }

        echo json_encode($users);
        $stmt->close();
        break;

    default:
        echo json_encode(["status"=>"error","message"=>"Invalid action"]);
        break;
}

$conn->close();
?>