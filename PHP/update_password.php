<?php
require 'config.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests are allowed');
    }

    // Get JSON input
    $json = file_get_contents('php://input');
    if (empty($json)) {
        throw new Exception('No data received');
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }

    // Validate required fields
    if (empty($data['token'])) {
        throw new Exception('Token is required');
    }
    
    if (empty($data['password'])) {
        throw new Exception('Password is required');
    }

    // Sanitize inputs
    $token = $conn->real_escape_string(trim($data['token']));
    $password = password_hash(trim($data['password']), PASSWORD_DEFAULT);

    // DEBUG: Log the received token
    error_log("Received token: $token");

    // Verify token with more detailed error reporting
    $result = $conn->query("SELECT email, expires_at, used FROM password_resets 
                          WHERE token = '$token'");

    if (!$result) {
        throw new Exception('Database error: ' . $conn->error);
    }

    if ($result->num_rows === 0) {
        throw new Exception('Token not found in database');
    }

    $row = $result->fetch_assoc();
    $email = $conn->real_escape_string($row['email']);
    $expires_at = $row['expires_at'];
    $used = $row['used'];
    
    // DEBUG: Log token details
    error_log("Token details: email=$email, expires_at=$expires_at, used=$used");

    // Check if token has been used
    if ($used) {
        throw new Exception('Token has already been used');
    }

    // Check if token is expired
    $current_time = date('Y-m-d H:i:s');
    if ($expires_at < $current_time) {
        throw new Exception('Token expired at ' . $expires_at);
    }

    // Update password
    if (!$conn->query("UPDATE users SET password = '$password' WHERE email = '$email'")) {
        throw new Exception('Password update failed: ' . $conn->error);
    }

    // Mark token as used
    $conn->query("UPDATE password_resets SET used = 1 WHERE token = '$token'");

    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully'
    ]);
    
} catch(Exception $e) {
    // DEBUG: Log the full error
    error_log("Update Password Error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>