<?php
header("Content-Type: application/json");
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

if ($id) {
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "User deleted successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error deleting user"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid user ID"]);
}

$conn->close();
?>
