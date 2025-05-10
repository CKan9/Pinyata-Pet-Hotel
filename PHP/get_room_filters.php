<?php
include 'config.php';
header('Content-Type: application/json');

$response = [
    'success' => false,
    'room_types' => [],
    'pet_types' => []
];

$sqlRoomTypes = "SELECT DISTINCT room_type FROM rooms ORDER BY room_type ASC";
$sqlPetTypes = "SELECT DISTINCT pet_type FROM rooms ORDER BY pet_type ASC";

$roomTypesResult = $conn->query($sqlRoomTypes);
$petTypesResult = $conn->query($sqlPetTypes);

if ($roomTypesResult && $petTypesResult) {
    while ($row = $roomTypesResult->fetch_assoc()) {
        $response['room_types'][] = $row['room_type'];
    }

    while ($row = $petTypesResult->fetch_assoc()) {
        $response['pet_types'][] = $row['pet_type'];
    }

    $response['success'] = true;
}

echo json_encode($response);
?>
