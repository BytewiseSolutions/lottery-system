<?php
// Add this to the top of your main index.php or any frequently visited page
// This will automatically manage draws when users visit your site

$lastCheck = '/tmp/last_draw_check.txt';
$now = time();

// Check if we need to run draw management (every 5 minutes)
if (!file_exists($lastCheck) || ($now - filemtime($lastCheck)) > 300) {
    // Call the upcoming-draws endpoint
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.totalfreelotto.com/api/upcoming-draws');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    curl_close($ch);
    
    // Update the last check time
    file_put_contents($lastCheck, $now);
}
?>