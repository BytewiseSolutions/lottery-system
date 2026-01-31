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
    $emailBody = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px 0;">
            <tr>
                <td align="center">
                    <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <!-- Header -->
                        <tr>
                            <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px 40px; text-align: center;">
                                <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">🎰 Total Free Lotto</h1>
                                <p style="margin: 10px 0 0 0; color: #ffffff; font-size: 14px; opacity: 0.9;">Your Chance to Win Big!</p>
                            </td>
                        </tr>
                        
                        <!-- Content -->
                        <tr>
                            <td style="padding: 40px;">
                                <div style="color: #333333; font-size: 16px; line-height: 1.6;">
                                    ' . nl2br(htmlspecialchars($data['message'])) . '
                                </div>
                            </td>
                        </tr>
                        
                        <!-- CTA Button (optional) -->
                        <tr>
                            <td style="padding: 0 40px 40px 40px; text-align: center;">
                                <a href="https://totalfreelotto.com" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 6px; font-weight: bold; font-size: 16px;">Visit Website</a>
                            </td>
                        </tr>
                        
                        <!-- Footer -->
                        <tr>
                            <td style="background-color: #f8f9fa; padding: 30px 40px; border-top: 1px solid #e9ecef;">
                                <!-- Contact Info -->
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="text-align: center; padding-bottom: 20px;">
                                            <p style="margin: 0; color: #666; font-size: 14px; font-weight: bold;">Contact Us</p>
                                            <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">Email: support@totalfreelotto.com</p>
                                            <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">Phone: +27 (0) 123 456 789</p>
                                        </td>
                                    </tr>
                                </table>
                                
                                <!-- Social Media -->
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="text-align: center; padding: 20px 0;">
                                            <a href="https://facebook.com/totalfreelotto" style="display: inline-block; margin: 0 10px; text-decoration: none;">
                                                <img src="https://img.icons8.com/color/48/facebook.png" alt="Facebook" width="32" height="32" style="display: block;">
                                            </a>
                                            <a href="https://twitter.com/totalfreelotto" style="display: inline-block; margin: 0 10px; text-decoration: none;">
                                                <img src="https://img.icons8.com/color/48/twitter.png" alt="Twitter" width="32" height="32" style="display: block;">
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                
                                <!-- Unsubscribe -->
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="text-align: center; padding-top: 20px; border-top: 1px solid #e9ecef;">
                                            <p style="margin: 0; color: #999; font-size: 12px;">
                                                You are receiving this email because you are a member of Total Free Lotto.<br>
                                                <a href="https://totalfreelotto.com/unsubscribe" style="color: #667eea; text-decoration: none;">Unsubscribe</a> | 
                                                <a href="https://totalfreelotto.com/privacy" style="color: #667eea; text-decoration: none;">Privacy Policy</a>
                                            </p>
                                            <p style="margin: 10px 0 0 0; color: #999; font-size: 11px;">
                                                © ' . date('Y') . ' Total Free Lotto. All rights reserved.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
    ';
    
    foreach ($recipients as $recipient) {
        $personalizedBody = str_replace('[NAME]', $recipient['full_name'], $emailBody);
        if (mail($recipient['email'], $subject, $personalizedBody, $headers)) {
            $sentCount++;
        }
        error_log("Notification sent to: " . $recipient['email']);
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
