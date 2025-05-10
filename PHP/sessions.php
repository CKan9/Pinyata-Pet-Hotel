<?php
session_start();
header('Content-Type: application/json');

include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(["success" => false, "message" => "User not logged in", "session_data" => $_SESSION]);
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role']; // Assuming role is stored in session

// Query selection based on role
if ($user_role == 'staff') {
    $sql = "SELECT full_name AS username, email FROM admin WHERE admin_id = ?";
} else {
    $sql = "SELECT username, email, phone FROM users WHERE user_id = ?";
}

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Common response fields
        $response = [
            "success" => true,
            "username" => $row["username"],
            "email" => $row["email"]
        ];

        // Include phone only for users
        if ($user_role !== 'staff' && isset($row["phone"])) {
            $response["phone"] = $row["phone"];
        }

        echo json_encode($response);
    } else {
        echo json_encode(["success" => false, "message" => "User not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Database query failed"]);
}

$conn->close();
?>
