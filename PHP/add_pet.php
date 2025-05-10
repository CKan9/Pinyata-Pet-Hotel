<?php
session_start();
include '../PHP/config.php'; // Ensure this connects to your database

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$pet_name = $_POST['pet_name'];
$pet_type = $_POST['pet_type'];
$breed = $_POST['breed'];
$age = $_POST['age'];
$special_notes = $_POST['special_notes'] ?? '';

// Handle File Upload
$target_dir = "../IMG/uploads/";

// Ensure the uploads directory exists
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$target_file = $target_dir . basename($_FILES["pet_picture"]["name"]);
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Validate image file
$check = getimagesize($_FILES["pet_picture"]["tmp_name"]);
if ($check === false) {
    echo json_encode(["success" => false, "message" => "File is not an image."]);
    exit();
}

$allowed_types = ["jpg", "jpeg", "png", "gif"];
if (!in_array($imageFileType, $allowed_types)) {
    echo json_encode(["success" => false, "message" => "Only JPG, JPEG, PNG & GIF files are allowed."]);
    exit();
}

if (!move_uploaded_file($_FILES["pet_picture"]["tmp_name"], $target_file)) {
    echo json_encode(["success" => false, "message" => "Error uploading file."]);
    exit();
}

// Insert pet details into the database
$sql = "INSERT INTO pets (user_id, pet_name, pet_type, breed, age, picture_url, special_notes) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssiss", $user_id, $pet_name, $pet_type, $breed, $age, $target_file, $special_notes);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Pet added successfully!"]);
} else {
    echo json_encode(["success" => false, "message" => "Error adding pet: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
