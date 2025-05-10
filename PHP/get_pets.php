<?php
session_start();
include '../PHP/config.php'; // Ensure this file connects to your database

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT pet_id, pet_name, pet_type, breed, age, picture_url, special_notes FROM pets WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$pets = [];
while ($row = $result->fetch_assoc()) {
    $pets[] = $row;
}

echo json_encode(["success" => true, "pets" => $pets]);

$stmt->close();
$conn->close();
?>
