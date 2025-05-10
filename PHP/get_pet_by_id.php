<?php
session_start();
include '../PHP/config.php';

header('Content-Type: application/json'); // Ensure JSON output

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

if (!isset($_GET['pet_id'])) {
    echo json_encode(["success" => false, "message" => "No pet ID provided"]);
    exit();
}

$pet_id = $_GET['pet_id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT pet_id, pet_name, pet_type, breed, age, special_notes, picture_url FROM pets WHERE pet_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $pet_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Ensure the correct path is sent to frontend
    $row['picture_url'] = $row['picture_url'] ? '../uploads/' . $row['picture_url'] : null;
    
    echo json_encode(["success" => true, "pet" => $row]);
} else {
    echo json_encode(["success" => false, "message" => "Pet not found"]);
}

$stmt->close();
$conn->close();

?>
