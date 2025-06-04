<?php
session_start();
include '../PHP/config.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not authorized"]);
    exit();
}

// Check if admin (you'll need to implement this check)
$is_admin = checkIfAdmin($_SESSION['user_id']); // Implement this function

if (!$is_admin) {
    echo json_encode(["success" => false, "message" => "Admin access required"]);
    exit();
}

// Process form data
$pet_id = $_POST['pet_id'] ?? null;
$pet_name = $_POST['pet_name'] ?? '';
$pet_type = $_POST['pet_type'] ?? '';
$breed = $_POST['breed'] ?? '';
$age = $_POST['age'] ?? 0;
$special_notes = $_POST['special_notes'] ?? '';

// Handle file upload
$picture_url = $_POST['existing_picture'] ?? '';
if (isset($_FILES['pet_picture']) && $_FILES['pet_picture']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["pet_picture"]["name"]);
    if (move_uploaded_file($_FILES["pet_picture"]["tmp_name"], $target_file)) {
        $picture_url = basename($_FILES["pet_picture"]["name"]);
    }
}

// Update pet in database
$sql = "UPDATE pets SET 
        pet_name = ?, 
        pet_type = ?, 
        breed = ?, 
        age = ?, 
        special_notes = ?, 
        picture_url = ?
        WHERE pet_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssissi", $pet_name, $pet_type, $breed, $age, $special_notes, $picture_url, $pet_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Pet updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error updating pet: " . $stmt->error]);
}

$stmt->close();
$conn->close();

// Function to check if user is admin
function checkIfAdmin($user_id) {
    // Implement your logic to check if the user is an admin
    // This might involve querying your users table for a role column
    // For now, we'll return true (you should replace this with actual logic)
    return true;
}
?>