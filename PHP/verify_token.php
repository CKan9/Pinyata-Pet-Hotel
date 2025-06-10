<?php
require 'config.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_GET['token'])) {
        throw new Exception('Token parameter missing');
    }

    $token = $conn->real_escape_string($_GET['token']);

    $result = $conn->query("SELECT * FROM password_resets 
                          WHERE token = '$token' 
                          AND used = 0 
                          AND expires_at > NOW()");

    if (!$result || $result->num_rows === 0) {
        throw new Exception('Invalid or expired token');
    }

    $reset = $result->fetch_assoc();
    echo json_encode(['success' => true, 'email' => $reset['email']]);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>