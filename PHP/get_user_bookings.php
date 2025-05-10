<?php
session_start();
require_once('config.php');

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$date = $_GET['date'] ?? '';
$pet_id = $_GET['pet_id'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "SELECT booking_id, pet_id, start_date, end_date, booking_status 
        FROM bookings 
        WHERE user_id = ?";
$params = [$user_id];
$types = 'i';

if (!empty($date)) {
    $sql .= " AND (? BETWEEN start_date AND end_date)";
    $params[] = $date;
    $types .= 's';
}

if (!empty($pet_id)) {
    $sql .= " AND pet_id = ?";
    $params[] = $pet_id;
    $types .= 's';
}

if (!empty($status)) {
    $sql .= " AND booking_status = ?";
    $params[] = $status;
    $types .= 's';
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed']);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

echo json_encode(['success' => true, 'bookings' => $bookings]);
?>
