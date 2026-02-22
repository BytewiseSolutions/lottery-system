<?php
// Test if mail() function is available and working
// Upload this file to your Hostinger server and access it via browser
// Example: https://totalfreelotto.com/test-mail.php

$to = 'totalfreelotto494@gmail.com';
$subject = 'Test Email from Hostinger';
$message = 'This is a test email to check if PHP mail() is working on your Hostinger server.';
$headers = 'From: totalfreelotto494@gmail.com' . "\r\n" .
           'Reply-To: totalfreelotto494@gmail.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

echo "<h2>PHP Mail Test</h2>";

// Check if mail function exists
if (function_exists('mail')) {
    echo "<p>✅ mail() function is available</p>";
    
    // Try to send email
    $result = mail($to, $subject, $message, $headers);
    
    if ($result) {
        echo "<p>✅ Email sent successfully!</p>";
        echo "<p>Check your inbox at: $to</p>";
        echo "<p><strong>Note:</strong> Check spam folder if you don't see it in inbox.</p>";
    } else {
        echo "<p>❌ Email failed to send</p>";
        echo "<p>This means mail() is available but not configured properly.</p>";
        echo "<p>You may need to use SMTP instead.</p>";
    }
} else {
    echo "<p>❌ mail() function is NOT available</p>";
    echo "<p>You need to use SMTP for sending emails.</p>";
}

echo "<hr>";
echo "<p><strong>Server Info:</strong></p>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "</ul>";

echo "<p style='color: red;'><strong>IMPORTANT:</strong> Delete this file after testing for security!</p>";
?>
