<?php
// Enable error logging and disable display
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');
header('Content-Type: application/json');

try {
    session_start();
    require_once '../PHP/config.php'; // Use require_once for critical includes

    // Validate session and input
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not logged in", 401);
    }

    if (!isset($_POST['pet_id']) || empty($_POST['pet_id'])) {
        throw new Exception("Pet ID is required", 400);
    }

    $pet_id = (int)$_POST['pet_id'];
    $user_id = (int)$_SESSION['user_id'];

    // Validate ownership
    $stmt = $conn->prepare("SELECT user_id FROM pets WHERE pet_id = ?");
    $stmt->bind_param("i", $pet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Pet not found", 404);
    }
    
    if ($result->fetch_assoc()['user_id'] !== $user_id) {
        throw new Exception("Unauthorized access", 403);
    }

    // Process data
    $pet_name = htmlspecialchars($_POST['pet_name']);
    $pet_type = htmlspecialchars($_POST['pet_type']);
    $breed = htmlspecialchars($_POST['breed']);
    $age = (int)$_POST['age'];
    $special_notes = htmlspecialchars($_POST['special_notes'] ?? '');
    $picture_url = null;

    // Handle file upload
    if (!empty($_FILES['pet_picture']['name']) && is_uploaded_file($_FILES['pet_picture']['tmp_name'])) {
        $upload_dir = realpath("../IMG/uploads/") . DIRECTORY_SEPARATOR;
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        // Validate file type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $file_type = $finfo->file($_FILES['pet_picture']['tmp_name']);
        
        if (!in_array($file_type, $allowed_types)) {
            throw new Exception("Invalid file type. Only JPG, PNG, and GIF are allowed", 415);
        }

        // Create directory if needed
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($_FILES['pet_picture']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('pet_', true) . '.' . $extension;
        $target_path = $upload_dir . $filename;

        if (!move_uploaded_file($_FILES['pet_picture']['tmp_name'], $target_path)) {
            throw new Exception("Failed to save uploaded file", 500);
        }

        $picture_url = "../IMG/uploads/" . $filename;
    }

    // Build SQL query
    $sql = "UPDATE pets SET 
            pet_name = ?, 
            pet_type = ?, 
            breed = ?, 
            age = ?, 
            special_notes = ?" 
            . ($picture_url ? ", picture_url = ?" : "") . 
            " WHERE pet_id = ?";

    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $params = [$pet_name, $pet_type, $breed, $age, $special_notes];
    if ($picture_url) {
        $params[] = $picture_url;
    }
    $params[] = $pet_id;

    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception("Database update failed: " . $stmt->error, 500);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Pet updated successfully',
        'redirect' => '../HTML/pets.html'
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    error_log('Pet Update Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
    exit();
}
?>