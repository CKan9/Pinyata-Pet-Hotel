<?php
session_start();
include '../PHP/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(["success" => false, "message" => "No booking ID provided"]);
    exit();
}

$booking_id = $_GET['id'];

// First check if the current user is admin (you'll need to implement this check)
$is_admin = checkIfAdmin($_SESSION['user_id']); // Implement this function

if ($is_admin) {
    // Admin can access any booking
    $sql = "SELECT booking_id, user_id, pet_id, room_id, start_date, end_date, 
                   total_amount, booking_status
            FROM bookings 
            WHERE booking_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
} else {
    // Regular users can only access their own bookings
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT booking_id, user_id, pet_id, room_id, start_date, end_date, 
                   total_amount, booking_status
            FROM bookings 
            WHERE booking_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $booking_id, $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "booking" => $row]);
} else {
    echo json_encode(["success" => false, "message" => "Booking not found"]);
}

$stmt->close();
$conn->close();

// Function to check if user is admin (you need to implement this based on your user roles)
function checkIfAdmin($user_id) {
    // Implement your logic to check if the user is an admin
    // This might involve querying your users table for a role column
    // For now, we'll return true (you should replace this with actual logic)
    return true;
}
?>