<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';

// Set response type to JSON
header('Content-Type: application/json');

// Read the raw JSON input
$input = json_decode(file_get_contents('php://input'), true);

// 🔍 Debugging: Check if JSON is received correctly
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON received.', 'debug' => file_get_contents('php://input')]);
    exit;
}

// Extract and validate required fields
$username = trim($input['username'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$password = $input['newPassword'] ?? '';

if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

// Hash the password securely
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Check if email already exists (to prevent duplicate registrations)
$check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$check_stmt->bind_param('s', $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered.']);
    exit;
}
$check_stmt->close();

// Insert user into the database
$stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $username, $email, $phone, $hashed_password);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Registration successful!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to register user.', 'error' => $conn->error]);
}

// Close database connections
$stmt->close();
$conn->close();
?>
