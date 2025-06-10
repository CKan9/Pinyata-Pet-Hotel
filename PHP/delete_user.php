<?php
header("Content-Type: application/json");
include 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

if ($id) {
    // Start transaction
    $conn->begin_transaction();

    try {
        // 1. Delete user's pets
        $stmt_pets = $conn->prepare("DELETE FROM pets WHERE user_id = ?");
        $stmt_pets->bind_param("i", $id);
        $stmt_pets->execute();
        $stmt_pets->close();

        // 2. Delete the user
        $stmt_user = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt_user->bind_param("i", $id);
        $stmt_user->execute();
        $stmt_user->close();

        // Commit changes
        $conn->commit();
        echo json_encode(["success" => true, "message" => "User and associated pets deleted successfully"]);
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid user ID"]);
}

$conn->close();
?>