<?php

require_once __DIR__ . '/autoload.php'; // Correct path to autoload

use Dotenv\Dotenv;

// Load .env
try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../'); // Load from project root
    $dotenv->load();
} catch (Exception $e) {
    // If .env loading fails, redirect or handle error
    header("Location: /Error/404.php");
    exit;
}

// PayPal API Credentials
define("CLIENT_ID", $_ENV['PAYPAL_CLIENT_ID']);
define("CLIENT_SECRET", $_ENV['PAYPAL_CLIENT_SECRET']);
define("PAYPAL_MODE", $_ENV['PAYPAL_MODE'] ?? 'sandbox'); // fallback to 'sandbox' if not set

// Return URLs
define("SUCCESS_URL", "/vendor/paypal_payment_success.php");
define("ERR_URL", "/vendor/paypal_payment_error.php");

// Custom error message for HTTP errors from PayPal
function PAYPAL_HTTP_ERR_MSG(int $http_code)
{
    $messages = [
        400 => "Request is not well-formed, check cURL format",
        401 => "PayPal credentials not matched",
        404 => "The specified resource not found",
        405 => "Method not allowed",
        406 => "Media type not acceptable, check [Accept] in header",
        409 => "There are conflicts with another request",
        415 => "Unsupported media type, check [Content-Type] in header",
        422 => "Request action cannot be processed",
        429 => "Too many requests / token exceeds a predefined value",
        500 => "PayPal internal server error, try again later",
        503 => "PayPal service unavailable, try again later"
    ];

    return $messages[$http_code] ?? "PayPal Uncaught Error";
}
