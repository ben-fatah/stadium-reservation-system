<?php
header('Content-Type: application/json; charset=utf-8');
include '../config/db.php';
// auth optional: if you want only logged in users, uncomment next two lines
// include '../Authentication/auth.php';
// if (!isset($_SESSION['user_id'])) { echo json_encode([]); exit(); }

$location  = isset($_GET['location']) ? trim($_GET['location']) : '';
$date      = isset($_GET['date']) ? trim($_GET['date']) : '';
$time_slot = isset($_GET['time_slot']) ? trim($_GET['time_slot']) : '';

// Base query: left join bookings so stadiums without slots still appear (slot_id NULL)
$sql = "
    SELECT 
        s.id AS stadium_id,
        s.name,
        s.location,
        s.description,
        s.photo,
        b.id AS slot_id,
        b.date,
        b.time_slot,
        b.status
    FROM stadiums s
    LEFT JOIN bookings b ON s.id = b.stadium_id
    WHERE 1=1
";

$params = [];
$types = '';

// filters
if ($location !== '') {
    $sql .= " AND s.location LIKE ?";
    $params[] = "%$location%";
    $types .= 's';
}
if ($date !== '') {
    $sql .= " AND b.date = ?";
    $params[] = $date;
    $types .= 's';
}
if ($time_slot !== '') {
    $sql .= " AND b.time_slot = ?";
    $params[] = $time_slot;
    $types .= 's';
}

$sql .= " ORDER BY s.name ASC, b.date ASC, b.time_slot ASC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = [
        "stadium_id" => (int)$r['stadium_id'],
        "name" => $r['name'],
        "location" => $r['location'],
        "description" => $r['description'],
        "photo" => $r['photo'],
        "slot_id" => $r['slot_id'] === null ? null : (int)$r['slot_id'],
        "date" => $r['date'],
        "time_slot" => $r['time_slot'],
        "status" => $r['status'] === null ? 'no-slot' : $r['status']
    ];
}

$stmt->close();
echo json_encode($rows);
$conn->close();
?>
