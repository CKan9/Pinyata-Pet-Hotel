<?php
session_start();
header("Content-Type: application/json");
include 'config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

$username = isset($data['username']) ? trim($data['username']) : '';
$phone = isset($data['phone']) ? trim($data['phone']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

if (!empty($username) && !empty($phone)) {
    if (!empty($password)) {
        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET username=?, phone=?, password=? WHERE user_id=?");
        $stmt->bind_param("sssi", $username, $phone, $hashedPassword, $user_id);
    } else {
        // Update without changing the password
        $stmt = $conn->prepare("UPDATE users SET username=?, phone=? WHERE user_id=?");
        $stmt->bind_param("ssi", $username, $phone, $user_id);
    }

    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
        exit;
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating user: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid input. Please check your data."]);
}

$conn->close();
?>
