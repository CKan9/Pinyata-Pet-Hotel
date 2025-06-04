<?php
session_start();
include '../PHP/config.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not authorized"]);
    exit();
}

// Get the target user_id from the request
if (!isset($_GET['user_id'])) {
    echo json_encode(["success" => false, "message" => "User ID required"]);
    exit();
}

$user_id = $_GET['user_id']; // Use the passed user_id

$sql = "SELECT pet_id, pet_name, pet_type, breed, age, picture_url, special_notes FROM pets WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the target user_id
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