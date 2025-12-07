<?php 
header('Content-Type: application/json'); 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// FIXED: Correct path to database.php (two levels up)
include __DIR__ . '/../../config/database.php';
include __DIR__ . '/../Authentication/auth.php'; 

if (!isset($_SESSION['user_id'])) { 
    echo json_encode(["status" => "error", "message" => "Unauthorized"]); 
    exit; 
} 

$user_id = $_SESSION['user_id']; 

$stmt = $conn->prepare(" 
    SELECT
        b.id AS slot_id,
        s.name AS stadium_name,
        b.date,
        b.time_slot,
        b.status
    FROM bookings b 
    JOIN stadiums s ON b.stadium_id = s.id 
    WHERE b.user_id = ? 
    ORDER BY b.date ASC, b.time_slot ASC
"); 

if (!$stmt) { 
    echo json_encode(["status" => "error", "message" => "Query error: " . $conn->error]); 
    exit; 
} 

$stmt->bind_param("i", $user_id); 
$stmt->execute(); 
$result = $stmt->get_result(); 

$reservations = []; 
while ($row = $result->fetch_assoc()) { 
    $reservations[] = $row; 
} 

echo json_encode(["status" => "success", "reservations" => $reservations]); 

$stmt->close(); 
$conn->close(); 
?>