<?php
// Set content type to JSON first
header('Content-Type: application/json');

// Start session and include config
session_start();
require_once('config.php');

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Get parameters from request with proper null checks
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$date = isset($_GET['date']) ? $_GET['date'] : '';
$pet_id = isset($_GET['pet_id']) ? $_GET['pet_id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Validate user_id
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

// Initialize response array
$response = ['success' => false, 'message' => ''];

try {
    // Prepare base query
    $sql = "SELECT booking_id, pet_id, start_date, end_date, booking_status 
            FROM bookings 
            WHERE user_id = ?";
    $params = [$user_id];
    $types = 'i';

    // Add filters
    if (!empty($date)) {
        $sql .= " AND (? BETWEEN start_date AND end_date)";
        $params[] = $date;
        $types .= 's';
    }

    if (!empty($pet_id)) {
        $sql .= " AND pet_id = ?";
        $params[] = $pet_id;
        $types .= 's';
    }

    if (!empty($status)) {
        $sql .= " AND booking_status = ?";
        $params[] = $status;
        $types .= 's';
    }

    // Prepare statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    // Execute query
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    // Get results
    $result = $stmt->get_result();
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }

    // Return success response
    $response = [
        'success' => true,
        'bookings' => $bookings
    ];

} catch (Exception $e) {
    $response['message'] = "Database error: " . $e->getMessage();
}

// Ensure we only output JSON
echo json_encode($response);
exit;
?>