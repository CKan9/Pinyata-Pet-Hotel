<?php
session_start();
require_once(__DIR__ . '/paypal_1_config.php');
require_once(__DIR__ . '/paypal_2_accessToken.php');
include '../PHP/config.php'; // DB connection

if (!isset($_GET['token'])) {
    die("No token provided");
}

$token = $_GET['token'];
$accessToken = getAccessToken();

// Step 1: Capture the payment
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

// Step 2: Update bookings and insert payment record
$booking_ids = $_SESSION['paypal_booking_id'];
$amount = $_SESSION['paypal_amount'];
$payment_date = date('Y-m-d H:i:s');
$payment_method = 'PayPal';
$payment_status = 'Completed';

foreach ($booking_ids as $booking_id) {
    // Update booking status
    $stmt = $conn->prepare("UPDATE bookings SET booking_status = 'Paid' WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();

    // Insert payment record
    $stmt2 = $conn->prepare("INSERT INTO payments (booking_id, payment_date, amount, payment_method, payment_status) 
                             VALUES (?, ?, ?, ?, ?)");
    $stmt2->bind_param("isdss", $booking_id, $payment_date, $amount, $payment_method, $payment_status);
    $stmt2->execute();
    $stmt2->close();
}

$conn->close();

// Step 3: Show success message
echo "
    <h2>Payment Successful!</h2>
    <p>Your booking has been confirmed.</p>
    <p>You will be redirected back to the booking page in 5 seconds...</p>

    <script>
        setTimeout(function() {
            window.location.href = '../HTML/Booking.html'; 
        }, 5000); // 5000 milliseconds = 5 seconds
    </script>
";
unset($_SESSION['paypal_amount']);
unset($_SESSION['paypal_booking_id']); // Optional: clear session values

?>
