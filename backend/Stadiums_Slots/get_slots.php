<?php 
header('Content-Type: application/json'); 
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL); 

session_start(); 
include '../../config/database.php';   
include '../Authentication/auth.php'; 

if (!isset($_SESSION['user_id'])) { 
    echo json_encode(["status" => "error", "message" => "Unauthorized"]); 
    exit; 
} 

$location = $_GET['location'] ?? ''; 
$date = $_GET['date'] ?? ''; 

$query = "
    SELECT b.id AS slot_id, s.name, s.location, b.date, b.time_slot, b.status
    FROM bookings b
    JOIN stadiums s ON b.stadium_id = s.id
    WHERE b.status = 'available'
";

$params = [];
$types = "";

// Filter by location if provided
if (!empty($location)) {
    $query .= " AND s.location LIKE ?";
    $params[] = "%" . $location . "%";
    $types .= "s";
}

// Filter by date if provided
if (!empty($date)) {
    $query .= " AND b.date = ?";
    $params[] = $date;
    $types .= "s";
}

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$slots = [];
while ($row = $result->fetch_assoc()) {
    $slots[] = $row;
}

echo json_encode($slots);

$stmt->close();
$conn->close();
