<?php

require_once __DIR__ . '/paypal_1_config.php';

function getAccessToken()
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_USERPWD, CLIENT_ID . ":" . CLIENT_SECRET);

    $headers = [
        "Accept: application/json",
        "Accept-Language: en_US",
        "Content-Type: application/x-www-form-urlencoded"
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        return null;
    }

    curl_close($ch);

    $resultObj = json_decode($result);
    if (!isset($resultObj->access_token)) {
        echo "<pre>Access Token Error:\n";
        print_r($resultObj);
        echo "</pre>";
        return null;
    }

    return $resultObj->access_token;
}

