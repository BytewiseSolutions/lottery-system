<?php
require_once 'config/cors.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$fileId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$fileId) {
    http_response_code(400);
    echo json_encode(['error' => 'File ID is required']);
    exit;
}

try {
    $query = "SELECT file_name, file_type, file_data FROM data_file WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$fileId]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'File not found']);
        exit;
    }
    
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Set appropriate headers
    header('Content-Type: ' . $file['file_type']);
    header('Content-Disposition: inline; filename="' . $file['file_name'] . '"');
    header('Cache-Control: public, max-age=31536000'); // Cache for 1 year
    
    // Output file data
    echo $file['file_data'];
    
} catch(PDOException $exception) {
    error_log("Get file error: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to retrieve file']);
}
?>
