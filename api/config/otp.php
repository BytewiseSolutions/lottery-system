<?php
class OTP {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function generateOTP() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    public function saveOTP($userId, $otpCode, $otpType) {
        $expiresAt = date('Y-m-d H:i:s', time() + 60); 
        
        $query = "INSERT INTO otp_verifications (user_id, otp_code, otp_type, expires_at) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$userId, $otpCode, $otpType, $expiresAt]);
    }
    
    public function verifyEmailToken($token) {
        $query = "SELECT user_id FROM email_verifications 
                  WHERE token = ? AND expires_at > NOW() AND is_used = FALSE";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$token]);
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $userId = $row['user_id'];
            
            // Mark token as used
            $updateQuery = "UPDATE email_verifications SET is_used = TRUE WHERE token = ?";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->execute([$token]);
            
            // Mark user as verified
            $userQuery = "UPDATE users SET email_verified = TRUE WHERE id = ?";
            $userStmt = $this->db->prepare($userQuery);
            $userStmt->execute([$userId]);
            
            return $userId;
        }
        return false;
    }
    
    public function sendEmailVerification($email, $token) {
        $verifyLink = "http://localhost:3002/api/verify-email?token=$token";
        
        $subject = "Verify Your Email - Lottery System";
        $message = "Click this link to verify your email: $verifyLink\n\nThis link expires in 24 hours.";
        $headers = "From: noreply@lottery-system.com";
        
        $sent = mail($email, $subject, $message, $headers);
        error_log($sent ? "Verification email sent to $email" : "Email failed to $email");
        return $sent;
    }
    
    public function generateVerificationToken($userId) {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 86400); // 24 hours
        
        $query = "INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $token, $expiresAt]);
        return $token;
    }
    
    public function sendSMSOTP($phone, $otpCode) {
        error_log("SMS OTP to $phone: $otpCode");
        
        if (getenv('APP_ENV') === 'development' || !getenv('SMS_ENABLED')) {
            error_log("SMS bypassed in development mode");
            return true;
        }
        
        $sid = getenv('TWILIO_SID');
        $token = getenv('TWILIO_TOKEN');
        $from = getenv('TWILIO_PHONE');
        
        if (!$sid || !$token || !$from) {
            error_log("Twilio credentials not configured");
            return false;
        }
        
        $message = "Your lottery verification code is: $otpCode. Valid for 60 seconds.";
        
        $data = [
            'From' => $from,
            'To' => $phone,
            'Body' => $message
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.twilio.com/2010-04-01/Accounts/$sid/Messages.json");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_USERPWD, "$sid:$token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $success = $httpCode === 201;
        error_log($success ? "SMS sent successfully" : "SMS failed: HTTP $httpCode");
        return $success;
    }
}
?>