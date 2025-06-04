<?php
session_start();
include '../PHP/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

if (!isset($_GET['pet_id'])) {
    echo json_encode(["success" => false, "message" => "No pet ID provided"]);
    exit();
}

$pet_id = $_GET['pet_id'];

// First check if the current user is admin (you'll need to implement this check)
$is_admin = checkIfAdmin($_SESSION['user_id']); // You need to implement this function

if ($is_admin) {
    // Admin can access any pet
    $sql = "SELECT pet_id, pet_name, pet_type, breed, age, special_notes, picture_url, user_id 
            FROM pets WHERE pet_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pet_id);
} else {
    // Regular users can only access their own pets
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT pet_id, pet_name, pet_type, breed, age, special_notes, picture_url, user_id 
            FROM pets WHERE pet_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $pet_id, $user_id);
}

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

// Function to check if user is admin (you need to implement this based on your user roles)
function checkIfAdmin($user_id) {
    // Implement your logic to check if the user is an admin
    // This might involve querying your users table for a role column
    // For now, we'll return true (you should replace this with actual logic)
    return true;
}
?>