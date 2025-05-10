<?php
include 'config.php';
header('Content-Type: application/json');

$conditions = [];
$params = [];

if (!empty($_GET['room_type'])) {
    $conditions[] = "room_type LIKE ?";
    $params[] = "%" . $_GET['room_type'] . "%";
}
if (!empty($_GET['pet_type'])) {
    $conditions[] = "pet_type LIKE ?";
    $params[] = "%" . $_GET['pet_type'] . "%";
}
if (!empty($_GET['availability'])) {
    $conditions[] = "availability = ?";
    $params[] = $_GET['availability'];
}

$sql = "SELECT * FROM rooms";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($sql);
if ($params) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

echo json_encode(['success' => true, 'rooms' => $rooms]);
?>
