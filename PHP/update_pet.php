<?php
session_start();
include '../PHP/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

if (!isset($_POST['pet_id'])) {
    echo json_encode(["success" => false, "message" => "Pet ID is missing"]);
    exit();
}

$pet_id = $_POST['pet_id'];
$user_id = $_SESSION['user_id'];
$pet_name = $_POST['pet_name'];
$pet_type = $_POST['pet_type'];
$breed = $_POST['breed'];
$age = $_POST['age'];
$special_notes = $_POST['special_notes'] ?? '';

// Default upload directories
$upload_dir = "../IMG/uploads/";
$web_img_path = "../IMG/uploads/";

// Step 1: Ensure existing_picture is valid
$picture_url = null;
if (isset($_POST['existing_picture']) && $_POST['existing_picture'] !== "0" && $_POST['existing_picture'] !== "") {
    $picture_url = $_POST['existing_picture'];
}

// Step 2: Handle new uploaded image
if (!empty($_FILES['pet_picture']['name']) && is_uploaded_file($_FILES['pet_picture']['tmp_name'])) {
    $file_type = mime_content_type($_FILES['pet_picture']['tmp_name']);
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];

    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(["success" => false, "message" => "Invalid file type"]);
        exit();
    }

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Save new file with unique name
    $unique_filename = uniqid() . "_" . basename($_FILES['pet_picture']['name']);
    $target_path = $upload_dir . $unique_filename;

    if (move_uploaded_file($_FILES['pet_picture']['tmp_name'], $target_path)) {
        $new_picture_url = $web_img_path . $unique_filename;

        // Delete old file if it's different
        if (!empty($picture_url)) {
            $existing_filename = basename($picture_url);
            $new_filename = basename($new_picture_url);
            if ($existing_filename !== $new_filename) {
                $old_path = $upload_dir . $existing_filename;
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
        }

        // Update picture_url to new one
        $picture_url = $new_picture_url;
    } else {
        echo json_encode(["success" => false, "message" => "Failed to move uploaded file"]);
        exit();
    }
}

// Step 3: Fallback if no image uploaded or missing in form
if (!$picture_url) {
    $stmt = $conn->prepare("SELECT picture_url FROM pets WHERE pet_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $pet_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $picture_url = $row['picture_url'];
    }
    $stmt->close();
}

// Step 4: Update the pet info in DB
if ($picture_url) {
    $sql = "UPDATE pets SET pet_name=?, pet_type=?, breed=?, age=?, special_notes=?, picture_url=? WHERE pet_id=? AND user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisssi", $pet_name, $pet_type, $breed, $age, $special_notes, $picture_url, $pet_id, $user_id);
} else {
    $sql = "UPDATE pets SET pet_name=?, pet_type=?, breed=?, age=?, special_notes=? WHERE pet_id=? AND user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissi", $pet_name, $pet_type, $breed, $age, $special_notes, $pet_id, $user_id);
}

file_put_contents("debug_update_log.txt", json_encode([
    "pet_id" => $pet_id,
    "user_id" => $user_id,
    "picture_url_final" => $picture_url,
    "existing_picture_input" => $_POST['existing_picture'],
    "new_file_uploaded" => $_FILES['pet_picture']['name'] ?? 'none'
], JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);


if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Pet updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Database update failed", "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
