<?php
// Minimal PHPMailer implementation for SMTP
class PHPMailer {
    public $Host = '';
    public $Port = 587;
    public $SMTPAuth = true;
    public $Username = '';
    public $Password = '';
    public $SMTPSecure = 'tls';
    public $From = '';
    public $FromName = '';
    public $Subject = '';
    public $Body = '';
    public $isHTML = false;
    private $to = [];
    private $replyTo = [];
    
    public function isSMTP() {}
    
    public function addAddress($email, $name = '') {
        $this->to[] = ['email' => $email, 'name' => $name];
    }
    
    public function addReplyTo($email, $name = '') {
        $this->replyTo[] = ['email' => $email, 'name' => $name];
    }
    
    public function send() {
        $headers = [];
        $headers[] = "From: {$this->FromName} <{$this->From}>";
        $headers[] = "MIME-Version: 1.0";
        
        if ($this->isHTML) {
            $headers[] = "Content-Type: text/html; charset=UTF-8";
        } else {
            $headers[] = "Content-Type: text/plain; charset=UTF-8";
        }
        
        if (!empty($this->replyTo)) {
            $headers[] = "Reply-To: {$this->replyTo[0]['email']}";
        }
        
        // Use SMTP via fsockopen
        $smtp = fsockopen($this->Host, $this->Port, $errno, $errstr, 30);
        if (!$smtp) {
            return false;
        }
        
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) != '220') {
            fclose($smtp);
            return false;
        }
        
        // EHLO
        fputs($smtp, "EHLO {$this->Host}\r\n");
        $response = fgets($smtp, 515);
        
        // STARTTLS
        if ($this->SMTPSecure == 'tls') {
            fputs($smtp, "STARTTLS\r\n");
            $response = fgets($smtp, 515);
            if (substr($response, 0, 3) != '220') {
                fclose($smtp);
                return false;
            }
            stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            fputs($smtp, "EHLO {$this->Host}\r\n");
            $response = fgets($smtp, 515);
        }
        
        // AUTH LOGIN
        if ($this->SMTPAuth) {
            fputs($smtp, "AUTH LOGIN\r\n");
            fgets($smtp, 515);
            fputs($smtp, base64_encode($this->Username) . "\r\n");
            fgets($smtp, 515);
            fputs($smtp, base64_encode($this->Password) . "\r\n");
            $response = fgets($smtp, 515);
            if (substr($response, 0, 3) != '235') {
                fclose($smtp);
                return false;
            }
        }
        
        // MAIL FROM
        fputs($smtp, "MAIL FROM: <{$this->From}>\r\n");
        fgets($smtp, 515);
        
        // RCPT TO
        foreach ($this->to as $recipient) {
            fputs($smtp, "RCPT TO: <{$recipient['email']}>\r\n");
            fgets($smtp, 515);
        }
        
        // DATA
        fputs($smtp, "DATA\r\n");
        fgets($smtp, 515);
        
        // Headers and body
        fputs($smtp, implode("\r\n", $headers) . "\r\n");
        fputs($smtp, "Subject: {$this->Subject}\r\n\r\n");
        fputs($smtp, $this->Body . "\r\n.\r\n");
        $response = fgets($smtp, 515);
        
        // QUIT
        fputs($smtp, "QUIT\r\n");
        fclose($smtp);
        
        return substr($response, 0, 3) == '250';
    }
}
