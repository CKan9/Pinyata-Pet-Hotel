<?php
require 'config.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $token = $conn->real_escape_string($data['token']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    // Verify token
    $result = $conn->query("SELECT email FROM password_resets 
                          WHERE token = '$token' 
                          AND used = 0 
                          AND expires_at > NOW()");

    if ($result->num_rows === 0) {
        throw new Exception('Invalid or expired token');
    }

    $row = $result->fetch_assoc();
    $email = $conn->real_escape_string($row['email']);

    // Update password
    if (!$conn->query("UPDATE users SET password = '$password' WHERE email = '$email'")) {
        throw new Exception('Password update failed');
    }

    // Mark token as used
    $conn->query("UPDATE password_resets SET used = 1 WHERE token = '$token'");

    echo json_encode(['success' => true]);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}