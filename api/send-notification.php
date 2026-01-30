<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config/database.php';
require_once 'config/jwt.php';

$user = JWT::authenticate();

if (!isset($user['role']) || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Admin access required']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['recipient_type']) || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $recipients = [];
    switch ($data['recipient_type']) {
        case 'all_users':
            $stmt = $db->query("SELECT email, full_name FROM users WHERE is_active = 1 AND email IS NOT NULL");
            $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'all_winners':
            $stmt = $db->query("
                SELECT DISTINCT u.email, u.full_name 
                FROM winners w 
                JOIN users u ON w.user_id = u.id 
                WHERE u.email IS NOT NULL
            ");
            $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'pending_winners':
            $stmt = $db->query("
                SELECT DISTINCT u.email, u.full_name 
                FROM winners w 
                JOIN users u ON w.user_id = u.id 
                WHERE w.status = 'pending' AND u.email IS NOT NULL
            ");
            $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
    }
    
    // Send emails
    $sentCount = 0;
    $fromEmail = $_ENV['SMTP_FROM'] ?? 'noreply@totalfreelotto.com';
    $headers = "From: Total Free Lotto <$fromEmail>\r\n";
    $headers .= "Reply-To: $fromEmail\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $subject = "Notification from Total Free Lotto";
    $emailBody = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>Total Free Lotto</h2>
        <p>" . nl2br(htmlspecialchars($data['message'])) . "</p>
        <hr>
        <p style='color: #666; font-size: 12px;'>This is an automated message from Total Free Lotto.</p>
    </body>
    </html>
    ";
    
    foreach ($recipients as $recipient) {
        if (mail($recipient['email'], $subject, $emailBody, $headers)) {
            $sentCount++;
        }
    }
    
    // Save notification to database
    $stmt = $db->prepare("
        INSERT INTO notifications (recipient_type, message, sent_by, sent_count) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $data['recipient_type'],
        $data['message'],
        $user['id'],
        $sentCount
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Notification sent successfully',
        'sent_count' => $sentCount
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
