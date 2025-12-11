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
    
    public function sendEmailOTP($email, $otpCode) {
        error_log("Email OTP to $email: $otpCode");
        
        // In development, log OTP and skip actual email sending
        if (getenv('APP_ENV') === 'development') {
            error_log("Development mode - OTP: $otpCode");
            return true;
        }
        
        // Use native PHP mail() - works with Hostinger
        $subject = "Your Lottery Verification Code";
        $message = "Your verification code is: $otpCode\n\nThis code expires in 60 seconds.\n\nIf you didn't request this code, please ignore this email.";
        $headers = "From: noreply@lottery-system.com\r\n";
        $headers .= "Reply-To: noreply@lottery-system.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        $sent = mail($email, $subject, $message, $headers);
        error_log($sent ? "Email sent to $email" : "Email failed to $email");
        return $sent;
    }
    
    public function sendSMSOTP($phone, $otpCode) {
        error_log("SMS OTP to $phone: $otpCode");
        error_log("SMS disabled - OTP logged only");
        return true;
    }
    
    public function verifyOTP($userId, $otpCode, $otpType) {
        $query = "SELECT id FROM otp_verifications 
                  WHERE user_id = ? AND otp_code = ? AND otp_type = ? 
                  AND expires_at > NOW() AND is_used = FALSE";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $otpCode, $otpType]);
        
        if ($stmt->rowCount() > 0) {
            // Mark OTP as used
            $updateQuery = "UPDATE otp_verifications SET is_used = TRUE 
                           WHERE user_id = ? AND otp_code = ? AND otp_type = ?";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->execute([$userId, $otpCode, $otpType]);
            return true;
        }
        return false;
    }
}
?>