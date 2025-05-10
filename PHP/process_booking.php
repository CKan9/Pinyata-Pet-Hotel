<?php
session_start();
include '../PHP/config.php';

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in";
    exit();
}

$user_id = $_SESSION['user_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$room_id = $_POST['room_id'];
$pet_ids = $_POST['pet_id']; // Array
$booking_status = 'Pending';

if (empty($start_date) || empty($end_date) || empty($room_id) || empty($pet_ids)) {
    echo "Missing required fields";
    exit();
}

// Calculate total
$total_days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
$sqlPrice = "SELECT price_per_day FROM rooms WHERE room_id = ?";
$stmtPrice = $conn->prepare($sqlPrice);
$stmtPrice->bind_param("i", $room_id);
$stmtPrice->execute();
$resultPrice = $stmtPrice->get_result();
$priceRow = $resultPrice->fetch_assoc();
$price_per_day = $priceRow['price_per_day'] ?? 0;
$total_amount = $total_days * $price_per_day;

$booking_ids = [];

foreach ($pet_ids as $pet_id) {
    $sqlInsert = "INSERT INTO bookings (user_id, pet_id, room_id, start_date, end_date, total_amount, booking_status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("iiissds", $user_id, $pet_id, $room_id, $start_date, $end_date, $total_amount, $booking_status);
    $stmtInsert->execute();
    $booking_ids[] = $stmtInsert->insert_id;
    $stmtInsert->close();
}

$conn->close();

// Save booking info in session for PayPal
$_SESSION['paypal_booking_id'] = $booking_ids;
$_SESSION['paypal_amount'] = $total_amount;

// Redirect to PayPal cart
header("Location: ../vendor/paypal_5_cart.php");
exit();
?>
