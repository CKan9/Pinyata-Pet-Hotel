<?php
session_start();
require_once(__DIR__ . '/paypal_config.php');
require_once(__DIR__ . '/paypal_accessToken.php');
include '../PHP/config.php';

if (!isset($_GET['token'])) {
    die("No token provided");
}

$token = $_GET['token'];
$accessToken = getAccessToken();

// Step 1: Capture payment
$ch = curl_init("https://api-m.sandbox.paypal.com/v2/checkout/orders/$token/capture");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $accessToken"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$paymentData = json_decode($response, true);

if (!isset($paymentData['status']) || $paymentData['status'] !== 'COMPLETED') {
    die("Payment not completed.");
}

// Retrieve pending booking data from session
if (!isset($_SESSION['pending_booking'])) {
    die("Booking data not found.");
}

$bookingData = $_SESSION['pending_booking'];
$user_id = $bookingData['user_id'];
$start_date = $bookingData['start_date'];
$end_date = $bookingData['end_date'];
$room_id = $bookingData['room_id'];
$pet_ids = $bookingData['pet_ids'];
$total_amount_myr = $bookingData['total_amount_myr']; // Get MYR amount from session

// Start transaction
$conn->begin_transaction();

try {
    $booking_ids = [];
    $payment_date = date('Y-m-d H:i:s');
    $payment_method = 'PayPal';
    $payment_status = 'Completed';

    // Insert bookings with MYR amount
    foreach ($pet_ids as $pet_id) {
        $stmt = $conn->prepare("
            INSERT INTO bookings 
                (user_id, pet_id, room_id, start_date, end_date, total_amount, booking_status) 
            VALUES (?, ?, ?, ?, ?, ?, 'Paid')
        ");
        $stmt->bind_param("iiissd", $user_id, $pet_id, $room_id, $start_date, $end_date, $total_amount_myr);
        $stmt->execute();
        $booking_ids[] = $conn->insert_id;
        $stmt->close();
    }

    // Insert payment record with MYR amount
    $stmt = $conn->prepare("
        INSERT INTO payments 
            (user_id, payment_date, amount, payment_method, payment_status) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isdss", $user_id, $payment_date, $total_amount_myr, $payment_method, $payment_status);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
}

// Clear session data
unset($_SESSION['pending_booking']);
unset($_SESSION['paypal_amount']);

// Show success message
echo "
    <h2>Payment Successful!</h2>
    <p>Your booking of MYR " . number_format($total_amount_myr, 2) . " has been confirmed.</p>
    <script>
        setTimeout(() => window.location.href = '../HTML/Booking.html', 5000);
    </script>
";
?>