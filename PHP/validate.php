<?php
include 'config.php';

$code = $_GET['code'] ?? '';
$email = $_GET['email'] ?? '';

if ($code && $email) {
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND validation_code = ?");
    $stmt->bind_param('ss', $email, $code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->close();

        $stmt = $conn->prepare("UPDATE users SET is_validated = 1, validation_code = NULL WHERE email = ?");
        $stmt->bind_param('s', $email);

        if ($stmt->execute()) {
            echo 'Your email has been validated successfully. You can now log in.';
        } else {
            echo 'Failed to validate your email.';
        }
    } else {
        echo 'Invalid validation code or email.';
    }
    $stmt->close();
} else {
    echo 'Invalid request.';
}

$conn->close();
?>
