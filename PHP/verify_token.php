<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        exit(json_encode(['success' => false, 'message' => 'Method Not Allowed']));
    }

    $token = $_GET['token'] ?? '';
    if (empty($token)) {
        throw new Exception('Missing token');
    }

    $stmt = $pdo->prepare("SELECT * FROM password_resets 
                          WHERE token = ? AND used = 0 AND expires_at > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();

    if (!$reset) {
        throw new Exception('Invalid or expired token');
    }

    echo json_encode(['success' => true, 'email' => $reset['email']]);
    
} catch (PDOException $e) {
    http_response_code(500);
    exit(json_encode(['success' => false, 'message' => 'Database error']));
} catch (Exception $e) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => $e->getMessage()]));
}
?>