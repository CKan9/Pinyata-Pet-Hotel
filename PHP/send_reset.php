<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// CORS headers
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mail_config.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit(json_encode(['success' => false, 'message' => 'Method Not Allowed']));
    }

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

    $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);

    // Database check
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if (!$stmt->fetch()) {
        throw new Exception('Email not found');
    }

    // Token generation
    $token = bin2hex(random_bytes(50));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$email, $token, $expires]);

    // Email sending
    $resetLink = "http://localhost/PT/HTML/reset_password.html?token=$token";
    
    $mail->clearAddresses();
    $mail->addAddress($email);
    $mail->Subject = 'Password Reset Request';
    $mail->Body = "Click to reset password: $resetLink (expires in 1 hour)";
    
    if(!$mail->send()) {
        throw new Exception('Mail sending failed: ' . $mail->ErrorInfo);
    }

    echo json_encode(['success' => true, 'message' => 'Reset link sent']);
    
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500);
    exit(json_encode(['success' => false, 'message' => 'Database error']));
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => $e->getMessage()]));
}
?>