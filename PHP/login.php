<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

$response = ['success' => false, 'message' => 'Invalid credentials.'];

// Check if username/email exists in admin table
$adminQuery = $conn->prepare("SELECT admin_id, email, password FROM admin WHERE email = ? LIMIT 1");
$adminQuery->bind_param("s", $username);
$adminQuery->execute();
$adminResult = $adminQuery->get_result();

if ($adminResult->num_rows > 0) {
    $admin = $adminResult->fetch_assoc();
    
    if (password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['admin_id'];
        $_SESSION['username'] = $admin['email'];
        $_SESSION['role'] = 'staff';

        $response = [
            'success' => true,
            'message' => 'Login successful (Staff)',
            'role' => 'staff',
            'username' => $admin['email']
        ];
    } else {
        $response['message'] = 'Invalid password.';
    }
} else {
    // Check if user exists in users table
    $userQuery = $conn->prepare("SELECT user_id, username, email, password FROM users WHERE (username = ? OR email = ?) LIMIT 1");
    $userQuery->bind_param("ss", $username, $username);
    $userQuery->execute();
    $userResult = $userQuery->get_result();

    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'guest';

            $response = [
                'success' => true,
                'message' => 'Login successful (Guest)',
                'role' => 'guest',
                'username' => $user['username']
            ];
        } else {
            $response['message'] = 'Invalid password.';
        }
    } else {
        $response['message'] = 'User not found.';
    }
}

$conn->close();
echo json_encode($response);
?>
