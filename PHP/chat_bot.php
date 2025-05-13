<?php
header('Content-Type: application/json');
require_once('../PHP/config.php');

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = $input['message'] ?? '';

// ========== Example 1: Check available rooms on a specific date ==========
if (preg_match('/room.*available.*(\d{4}-\d{2}-\d{2})/', $userMessage, $matches)) {
    $date = $matches[1];
    $sql = "SELECT COUNT(*) AS available_rooms FROM rooms WHERE room_id NOT IN (
        SELECT room_id FROM bookings WHERE '$date' BETWEEN start_date AND end_date
    )";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $reply = "🐾 We have " . $row['available_rooms'] . " room(s) available on $date. Would you like to book one?";
    echo json_encode(['reply' => $reply]);
    exit;
}

// ========== Example 2: Show types of rooms ==========
if (strpos($userMessage, 'what types of rooms') !== false || strpos($userMessage, 'room types') !== false) {
    $sql = "SELECT DISTINCT room_type FROM rooms";
    $result = $conn->query($sql);
    $types = [];
    while ($row = $result->fetch_assoc()) {
        $types[] = $row['room_type'];
    }
    $reply = "🏠 We offer the following types of rooms: " . implode(', ', $types) . ".";
    echo json_encode(['reply' => $reply]);
    exit;
}

// ========== Example 3: Supported pet types ==========
if (preg_match('/(what|which).*pet.*(support|allowed|accept|room)/i', $userMessage)) {
    $sql = "SELECT DISTINCT pet_type FROM rooms";
    $result = $conn->query($sql);
    $pets = [];
    while ($row = $result->fetch_assoc()) {
        $pets[] = ucfirst($row['pet_type']);
    }
    $reply = "🐾 We currently support rooms for the following pet types: " . implode(', ', $pets) . ".";
    echo json_encode(['reply' => $reply]);
    exit;
}

// Use Gemini API
$apiKey = "AIzaSyBHL29ZT7MebwlCCdHAHNecUuiXLL3fh2c"; // Replace with your actual key
$endpoint = "https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key=$apiKey";

$payload = [
    "contents" => [
        [
            "parts" => [
                [
                    "text" => "You are a friendly and helpful virtual assistant working for a pet hotel. Answer user questions in a polite, warm tone. If unsure, respond kindly and suggest contacting human support."
                ],
                [
                    "text" => $userMessage
                ]
            ]
        ]
    ]
];

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo json_encode(['reply' => 'Curl error: ' . curl_error($ch)]);
    exit;
}
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo json_encode([
        'reply' => "Oops! I couldn't reach the AI service. Please try again later.",
        'debug_http' => $httpCode,
        'debug_response' => $response
    ]);
    exit;
}


$responseData = json_decode($response, true);
$reply = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? "Sorry, I couldn’t understand.";
echo json_encode(['reply' => $reply]);
