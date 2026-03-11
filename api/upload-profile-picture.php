<?php
require_once 'config/cors.php';
require_once 'config/database.php';
require_once 'config/jwt.php';

$database = new Database();
$db = $database->getConnection();

$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_FILES['profilePicture']) || !isset($_POST['userId'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$userId = $_POST['userId'];
$file = $_FILES['profilePicture'];

// Validate file
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxSize = 5 * 1024 * 1024; // 5MB

if (!in_array($file['type'], $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, and GIF are allowed']);
    exit;
}

if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['error' => 'File size exceeds 5MB limit']);
    exit;
}

try {
    // Read file data
    $fileData = file_get_contents($file['tmp_name']);
    
    // Start transaction
    $db->beginTransaction();
    
    // Check if user already has a profile picture
    $checkQuery = "SELECT profile_picture FROM user WHERE id = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$userId]);
    $existingPicture = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    // Delete old profile picture if exists
    if ($existingPicture && $existingPicture['profile_picture']) {
        $deleteQuery = "DELETE FROM data_file WHERE id = ?";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->execute([$existingPicture['profile_picture']]);
    }
    
    // Insert new file into data_file table
    $insertQuery = "INSERT INTO data_file (user_id, file_name, file_type, file_size, file_data, file_category) 
                    VALUES (?, ?, ?, ?, ?, 'profile_picture')";
    $insertStmt = $db->prepare($insertQuery);
    $insertStmt->execute([
        $userId,
        $file['name'],
        $file['type'],
        $file['size'],
        $fileData
    ]);
    
    $fileId = $db->lastInsertId();
    
    // Update user table with file ID
    $updateQuery = "UPDATE user SET profile_picture = ? WHERE id = ?";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([$fileId, $userId]);
    
    // Commit transaction
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'profilePictureId' => $fileId,
        'message' => 'Profile picture uploaded successfully'
    ]);
    
} catch(Exception $exception) {
    // Rollback on error
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Upload profile picture error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to upload profile picture']);
}
?>
