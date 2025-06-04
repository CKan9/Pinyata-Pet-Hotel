<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

require __DIR__ . '/config.php';

$response = ['success' => false, 'message' => 'Not logged in'];

try {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        echo json_encode($response);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['role'];

    if ($user_role == 'staff') {
        $sql = "SELECT full_name AS username, email FROM admin WHERE admin_id = ?";
    } else {
        $sql = "SELECT username, email, phone FROM users WHERE user_id = ?";
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $response = [
            "success" => true,
            "username" => $row["username"],
            "email" => $row["email"]
        ];

        if ($user_role !== 'staff' && isset($row["phone"])) {
            $response["phone"] = $row["phone"];
        }
    } else {
        $response['message'] = 'User not found';
    }

    $stmt->close();
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
    error_log($e->getMessage());
}

$conn->close();
echo json_encode($response);
?>