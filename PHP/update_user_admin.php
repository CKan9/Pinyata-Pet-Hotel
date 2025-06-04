<?php
session_start();
header("Content-Type: application/json");
include 'config.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not authorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Retrieve the target user ID from the POST data
$target_user_id = isset($data['id']) ? (int)$data['id'] : 0;

// Validate input
$username = isset($data['username']) ? trim($data['username']) : '';
$phone = isset($data['phone']) ? trim($data['phone']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

if (empty($target_user_id) || empty($username) || empty($phone)) {
    echo json_encode(["success" => false, "message" => "Invalid input or user ID"]);
    exit;
}

// Update the TARGET user's data (not the admin's)
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET username=?, phone=?, password=? WHERE user_id=?");
    $stmt->bind_param("sssi", $username, $phone, $hashedPassword, $target_user_id);
} else {
    // Don't update password if not provided
    $stmt = $conn->prepare("UPDATE users SET username=?, phone=? WHERE user_id=?");
    $stmt->bind_param("ssi", $username, $phone, $target_user_id);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "User updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>