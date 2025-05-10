<?php
session_start();
include '../PHP/config.php';

header('Content-Type: application/json'); // Ensure JSON output

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(["success" => false, "message" => "No booking ID provided"]);
    exit();
}

$booking_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Secure query to get booking for this user
$sql = "SELECT booking_id, user_id, pet_id, room_id, start_date, end_date, total_amount, booking_status, managed_by 
        FROM bookings 
        WHERE booking_id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "booking" => $row]);
} else {
    echo json_encode(["success" => false, "message" => "Booking not found or access denied"]);
}

$stmt->close();
$conn->close();
?>
