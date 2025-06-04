<?php
require 'config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate email exists first
    $email = $conn->real_escape_string($_POST['email']);
    $result = $conn->query("SELECT user_id FROM users WHERE email = '$email'");
    
    if ($result->num_rows === 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Email not found']);
        exit;
    }

    // Generate token
    $token = bin2hex(random_bytes(16));
    $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    // Store token
    if (!$conn->query("INSERT INTO password_resets (email, token, expires_at) VALUES ('$email', '$token', '$expires')")) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit;
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'token' => $token,
        'redirect' => "reset_password.html?token=$token"
    ]);
    
} else {
    // Return HTML for GET requests
    echo '<form method="POST">
            <h2>Get Reset Token</h2>
            <label>Email: <input type="email" name="email" required></label>
            <button type="submit">Get Token</button>
          </form>';
}