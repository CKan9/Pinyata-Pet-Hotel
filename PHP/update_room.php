<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$required_fields = ['room_id', 'room_type', 'description', 'capacity', 'price_per_day', 'availability', 'pet_type'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'error' => "Missing field: $field"]);
        exit;
    }
}

$room_id = intval($_POST['room_id']);
$room_type = $_POST['room_type'];
$description = $_POST['description'];
$capacity = intval($_POST['capacity']);
$price_per_day = floatval($_POST['price_per_day']);
$availability = $_POST['availability'];
$pet_type = $_POST['pet_type'];

$sql = "UPDATE rooms SET room_type = ?, description = ?, capacity = ?, price_per_day = ?, availability = ?, pet_type = ? WHERE room_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssids si", $room_type, $description, $capacity, $price_per_day, $availability, $pet_type, $room_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Update failed']);
}

$stmt->close();
$conn->close();
?>
