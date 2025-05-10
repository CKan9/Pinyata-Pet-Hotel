<?php
session_start();
include '../PHP/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

$sql = "SELECT room_id, room_type, pet_type FROM rooms";
$result = $conn->query($sql);

$rooms = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}

echo json_encode($rooms);

$conn->close();
?>
