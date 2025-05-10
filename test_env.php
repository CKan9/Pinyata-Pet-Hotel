<?php
require_once __DIR__ . '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "Client ID: " . $_ENV['PAYPAL_CLIENT_ID'];
