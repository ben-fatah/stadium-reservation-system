<?php
include '../config/db.php';
include '../Authentication/auth.php';

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
                echo json_encode(["status"=>"success","message"=>"Message sent"]);
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
            SELECT m.id, m.sender_id, u.username AS sender_name, m.message, m.sent_at
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
            SELECT m.id, m.receiver_id, u.username AS receiver_name, m.message, m.sent_at
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

    default:
        echo json_encode(["status"=>"error","message"=>"Invalid action"]);
        break;
}

$conn->close();
?>
