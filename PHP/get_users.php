<?php
include 'config.php';
header('Content-Type: application/json');

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Check if search parameter exists
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Build SQL query
$sql = "SELECT user_id, username, email, phone FROM users";

if (!empty($searchTerm)) {
    // Add WHERE clause for search
    $searchTerm = $conn->real_escape_string($searchTerm);
    $sql .= " WHERE username LIKE '%$searchTerm%' 
              OR email LIKE '%$searchTerm%' 
              OR phone LIKE '%$searchTerm%'";
}

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["success" => false, "message" => "Query failed: " . $conn->error]);
    exit;
}

if ($result->num_rows > 0) {
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(["success" => true, "data" => $users]);
} else {
    echo json_encode(["success" => true, "data" => []]);
}
$conn->close();
?>