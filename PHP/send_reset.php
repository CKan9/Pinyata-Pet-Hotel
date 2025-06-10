<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mail_config.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get JSON input
    $json = file_get_contents('php://input');
    if (empty($json)) {
        throw new Exception('Empty request body');
    }
    
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }

    if (!isset($data['email']) || empty(trim($data['email']))) {
        throw new Exception('Email field is required');
    }

    $email = $conn->real_escape_string(trim($data['email']));

    // Check if email exists
    $result = $conn->query("SELECT user_id FROM users WHERE email = '$email'");
    if (!$result || $result->num_rows === 0) {
        throw new Exception('Email not found');
    }

    // Generate token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 900); // 15 minutes

    // Store token in database
    $sql = "INSERT INTO password_resets (email, token, expires_at) VALUES ('$email', '$token', '$expires')";
    if (!$conn->query($sql)) {
        throw new Exception('Database error: ' . $conn->error);
    }

    // Create reset link
    $resetLink = "http://localhost/PT/HTML/reset_password.html?token=$token";
    
    // Configure email
    $mail->addAddress($email);
    $mail->Subject = 'Password Reset Request';
    $mail->Body = "Click this link to reset your password:\n$resetLink\n\n";
    $mail->Body .= "This link will expire in 15 minutes.";
    
    // Send email
    if (!$mail->send()) {
        throw new Exception('Failed to send reset email: ' . $mail->ErrorInfo);
    }

    echo json_encode(['success' => true, 'message' => 'Reset link sent']);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>