<?php
include 'config.php';

if (isset($_GET['room_type'])) {
    $room_type = $_GET['room_type'];

    $stmt = $conn->prepare("SELECT id FROM rooms WHERE room_type = ? AND available > 0 ORDER BY id ASC");
    $stmt->bind_param("s", $room_type);
    $stmt->execute();
    $result = $stmt->get_result();

    $rooms = array();
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row['id'];
    }

    echo json_encode($rooms);
    $stmt->close();
}

$conn->close();
?>
