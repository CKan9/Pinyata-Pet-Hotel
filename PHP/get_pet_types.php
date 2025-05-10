<?php
session_start();
include '../PHP/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['pet_ids']) || !is_array($data['pet_ids']) || count($data['pet_ids']) === 0) {
    echo json_encode([]);
    exit();
}

$pet_ids = $data['pet_ids'];
$placeholders = implode(',', array_fill(0, count($pet_ids), '?'));
$types = str_repeat('i', count($pet_ids));

$sql = "SELECT DISTINCT pet_type FROM pets WHERE pet_id IN ($placeholders)";
$stmt = $conn->prepare($sql);

$stmt->bind_param($types, ...$pet_ids);
$stmt->execute();
$result = $stmt->get_result();

$petTypes = [];
while ($row = $result->fetch_assoc()) {
    $petTypes[] = $row['pet_type'];
}

echo json_encode($petTypes);

$stmt->close();
$conn->close();
?>
