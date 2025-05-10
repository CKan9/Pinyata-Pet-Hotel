<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['room_id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing room_id']);
    exit;
}

$room_id = intval($_GET['room_id']);

$sql = "SELECT * FROM rooms WHERE room_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $room = $result->fetch_assoc();
    echo json_encode(['success' => true, 'room' => $room]);
} else {
    echo json_encode(['success' => false, 'error' => 'Room not found']);
}

$stmt->close();
$conn->close();
?>
