<?php
session_start();
include '../PHP/config.php';

if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

// Get booking details from POST
$user_id = $_SESSION['user_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$room_id = $_POST['room_id'];
$pet_ids = $_POST['pet_id'];

// Validate inputs
if (empty($start_date) || empty($end_date) || empty($room_id) || empty($pet_ids)) {
    die("Missing required fields");
}

// Calculate total in MYR
$total_days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24);
$sqlPrice = "SELECT price_per_day FROM rooms WHERE room_id = ?";
$stmtPrice = $conn->prepare($sqlPrice);
$stmtPrice->bind_param("i", $room_id);
$stmtPrice->execute();
$resultPrice = $stmtPrice->get_result();
$priceRow = $resultPrice->fetch_assoc();
$price_per_day = $priceRow['price_per_day'] ?? 0;
$total_amount_myr = $total_days * $price_per_day;

// Get live exchange rate
function getExchangeRate() {
    $api_key = 'YOUR_EXCHANGE_RATE_API_KEY'; // Get from exchangerate-api.com
    $url = "https://v6.exchangerate-api.com/v6/{$api_key}/pair/MYR/USD";
    
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    if ($data['result'] === 'success') {
        return $data['conversion_rate'];
    }
    
    // Fallback rate if API fails
    return 0.21; // Approximate MYR to USD
}

$exchange_rate = getExchangeRate();
$total_amount_usd = $total_amount_myr * $exchange_rate;

// Store in session
$_SESSION['pending_booking'] = [
    'user_id' => $user_id,
    'start_date' => $start_date,
    'end_date' => $end_date,
    'room_id' => $room_id,
    'pet_ids' => $pet_ids,
    'total_amount_myr' => $total_amount_myr  // Store MYR in session
];

$_SESSION['paypal_amount'] = $total_amount_usd;  // USD amount for PayPal

// Redirect to PayPal
header("Location: ../vendor/paypal_create_order.php");
exit();
?>