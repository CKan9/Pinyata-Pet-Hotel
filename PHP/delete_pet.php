<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

// Validate input
if (!isset($_POST['pet_id']) || !is_numeric($_POST['pet_id'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid pet ID']));
}

$pet_id = $_POST['pet_id'];
$user_id = $_SESSION['user_id'];

// Database connection
require_once 'config.php';

try {
    // First check if the pet belongs to the user
    $stmt = $conn->prepare("SELECT user_id FROM pets WHERE pet_id = ?");
    $stmt->bind_param("i", $pet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        die(json_encode(['success' => false, 'message' => 'Pet not found']));
    }
    
    $pet = $result->fetch_assoc();
    if ($pet['user_id'] != $user_id) {
        http_response_code(403);
        die(json_encode(['success' => false, 'message' => 'You can only delete your own pets']));
    }
    
    // Delete the pet
    $stmt = $conn->prepare("DELETE FROM pets WHERE pet_id = ?");
    $stmt->bind_param("i", $pet_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Pet deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error deleting pet']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>