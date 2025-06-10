<?php
session_start();
header('Content-Type: application/json');

// Database connection
include 'config.php';

// Check for POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate required fields
$required_fields = ['booking_id', 'room_id', 'start_date', 'end_date', 'total_amount', 'booking_status'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
        exit;
    }
}

// Sanitize input
$booking_id = intval($_POST['booking_id']);
$room_id = intval($_POST['room_id']);
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$total_amount = floatval($_POST['total_amount']);
$booking_status = $_POST['booking_status'];

// Optional: validate date format
if (!strtotime($start_date) || !strtotime($end_date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
    exit;
}

// Update query
$sql = "UPDATE bookings 
        SET room_id = ?, start_date = ?, end_date = ?, total_amount = ?, booking_status = ?
        WHERE booking_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'SQL prepare error: ' . $conn->error]);
    exit;
}

// Bind parameters
$stmt->bind_param("issdsi", $room_id, $start_date, $end_date, $total_amount, $booking_status, $booking_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Booking updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
