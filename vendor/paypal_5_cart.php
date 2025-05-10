<?php
session_start();
require_once(__DIR__ . '/paypal_1_config.php');
require_once(__DIR__ . '/paypal_2_accessToken.php');

if (!isset($_SESSION['paypal_amount'])) {
    die("Error: PayPal amount not set in session.");
}

$accessToken = getAccessToken();
$amount = $_SESSION['paypal_amount'];
$returnUrl = 'http://localhost/PT/vendor/paypal_payment_success.php';
$cancelUrl = 'http://localhost/PT/vendor/paypal_payment_error.php';

// Create order
$orderData = [
    'intent' => 'CAPTURE',
    'purchase_units' => [[
        'amount' => [
            'currency_code' => 'USD',
            'value' => number_format($amount, 2, '.', '')
        ]
    ]],
    'application_context' => [
        'return_url' => $returnUrl,
        'cancel_url' => $cancelUrl
    ]
];

$ch = curl_init("https://api-m.sandbox.paypal.com/v2/checkout/orders");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $accessToken"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$order = json_decode($response);
foreach ($order->links as $link) {
    if ($link->rel === 'approve') {
        header("Location: " . $link->href);
        exit();
    }
}

echo "Unable to create PayPal payment.";
?>
