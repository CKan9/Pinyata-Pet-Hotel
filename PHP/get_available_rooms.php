<?php
session_start();
include '../PHP/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$start_date = $data['start_date'] ?? '';
$end_date = $data['end_date'] ?? '';
$pet_ids = $data['pet_ids'] ?? [];

if (!$start_date || !$end_date || !is_array($pet_ids) || count($pet_ids) === 0) {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
    exit();
}

// Step 1: Get the pet types based on selected pets
$placeholders = implode(',', array_fill(0, count($pet_ids), '?'));
$types = str_repeat('i', count($pet_ids));

$sqlPetTypes = "SELECT DISTINCT pet_type FROM pets WHERE pet_id IN ($placeholders)";
$stmt = $conn->prepare($sqlPetTypes);
$stmt->bind_param($types, ...$pet_ids);
$stmt->execute();
$result = $stmt->get_result();

$petTypes = [];
while ($row = $result->fetch_assoc()) {
    $petTypes[] = $row['pet_type'];
}
$stmt->close();

if (empty($petTypes)) {
    echo json_encode([]);
    exit();
}

// Step 2: Find available rooms
$placeholders = implode(',', array_fill(0, count($petTypes), '?'));
$types = str_repeat('s', count($petTypes));

$sql = "
    SELECT r.room_id, r.room_type, r.pet_type
    FROM rooms r
    WHERE r.pet_type IN ($placeholders)
    AND r.room_id NOT IN (
        SELECT room_id FROM bookings
        WHERE (start_date <= ? AND end_date >= ?)
    )
";

$stmt = $conn->prepare($sql);
$params = array_merge($petTypes, [$end_date, $start_date]);
$stmt->bind_param($types . 'ss', ...$params);
$stmt->execute();
$result = $stmt->get_result();

$availableRooms = [];
while ($row = $result->fetch_assoc()) {
    $availableRooms[] = $row;
}

echo json_encode($availableRooms);

$stmt->close();
$conn->close();
?>
