<?php
// Set headers FIRST to prevent CORB issues
header('Access-Control-Allow-Origin: https://pinyatapet.infinityfreeapp.com');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=UTF-8');

// Handle OPTIONS request for CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Error reporting
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/chat_errors.log');

// Database configuration
require_once('../PHP/config.php');

// Ensure we have POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['reply' => 'Method not allowed']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE || !isset($input['message'])) {
    http_response_code(400);
    echo json_encode(['reply' => 'Invalid request data']);
    exit;
}

$userMessage = trim($input['message']);

// ========== Database Responses ==========
// Check available rooms on a specific date
if (preg_match('/room.*available.*(\d{4}-\d{2}-\d{2})/i', $userMessage, $matches)) {
    $date = $matches[1];
    $stmt = $conn->prepare("SELECT COUNT(*) AS available_rooms FROM rooms 
                          WHERE room_id NOT IN (
                              SELECT room_id FROM bookings 
                              WHERE ? BETWEEN start_date AND end_date
                          )");
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo json_encode(['reply' => "🐾 We have " . $row['available_rooms'] . " room(s) available on $date. Would you like to book one?"]);
    exit;
}

// Show types of rooms
if (preg_match('/(what|which).*type.*room/i', $userMessage)) {
    $result = $conn->query("SELECT DISTINCT room_type FROM rooms");
    $types = [];
    while ($row = $result->fetch_assoc()) {
        $types[] = ucfirst($row['room_type']);
    }
    echo json_encode(['reply' => "🏠 We offer: " . implode(', ', $types)]);
    exit;
}

// Supported pet types
if (preg_match('/(what|which).*pet.*(support|allowed|accept|room)/i', $userMessage)) {
    $result = $conn->query("SELECT DISTINCT pet_type FROM rooms");
    $pets = [];
    while ($row = $result->fetch_assoc()) {
        $pets[] = ucfirst($row['pet_type']);
    }
    echo json_encode(['reply' => "🐾 We support: " . implode(', ', $pets)]);
    exit;
}

// ========== Gemini API Integration ==========
$apiKey = "AIzaSyBHL29ZT7MebwlCCdHAHNecUuiXLL3fh2c"; // Replace with your actual key
$endpoint = "https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key=$apiKey";

// System instruction for the AI
$systemInstruction = [
    "role" => "model",
    "parts" => [["text" => "You are a friendly pet hotel assistant. Provide helpful, concise answers about pet accommodations, services, and bookings. For non-hotel questions, politely decline. Keep responses under 3 sentences."]]
];

// User message
$userMessagePart = [
    "role" => "user",
    "parts" => [["text" => $userMessage]]
];

try {
    $payload = [
        "contents" => [
            $systemInstruction,
            $userMessagePart
        ],
        "generationConfig" => [
            "temperature" => 0.7,
            "topP" => 0.8,
            "topK" => 40,
            "maxOutputTokens" => 256
        ],
        "safetySettings" => [
            [
                "category" => "HARM_CATEGORY_HARASSMENT",
                "threshold" => "BLOCK_ONLY_HIGH"
            ],
            [
                "category" => "HARM_CATEGORY_HATE_SPEECH",
                "threshold" => "BLOCK_ONLY_HIGH"
            ],
            [
                "category" => "HARM_CATEGORY_SEXUALLY_EXPLICIT",
                "threshold" => "BLOCK_ONLY_HIGH"
            ],
            [
                "category" => "HARM_CATEGORY_DANGEROUS_CONTENT",
                "threshold" => "BLOCK_ONLY_HIGH"
            ]
        ]
    ];

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FAILONERROR => true
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($error) {
        throw new Exception("API connection failed: " . $error);
    }

    if ($httpCode !== 200) {
        throw new Exception("API returned HTTP $httpCode: " . substr($response, 0, 200));
    }

    $responseData = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON response");
    }

    if (empty($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        throw new Exception("No text in API response");
    }

    $reply = $responseData['candidates'][0]['content']['parts'][0]['text'];
    
    // Clean up any markdown or special characters
    $reply = preg_replace('/\*+/', '', $reply); // Remove markdown asterisks
    $reply = trim($reply);
    
    echo json_encode(['reply' => $reply]);

} catch (Exception $e) {
    error_log('Gemini API Error: ' . $e->getMessage());
    echo json_encode([
        'reply' => "I'm having trouble answering that. Please try again later or contact our front desk.",
        'error' => $e->getMessage()
    ]);
}