<?php
/**
 * Admin Password Hash Generator
 * 
 * Usage: php generate-admin-password.php
 * 
 * This script generates a secure password hash for the admin user.
 * Copy the generated hash and use it in the security-updates.sql file.
 */

echo "=== Admin Password Hash Generator ===\n\n";

// Default password - CHANGE THIS!
$password = 'Admin@2026!';

echo "Generating hash for password: $password\n";
echo "⚠️  IMPORTANT: Change this password before using in production!\n\n";

$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Generated Hash:\n";
echo "$hash\n\n";

echo "Copy this hash and update the security-updates.sql file:\n";
echo "Replace '\$2y\$10\$YourHashedPasswordHere' with the hash above.\n\n";

echo "Then run:\n";
echo "mysql -u your_user -p lottery_db < security-updates.sql\n\n";

if (password_verify($password, $hash)) {
    echo "✅ Hash verification successful!\n";
} else {
    echo "❌ Hash verification failed!\n";
}
?>
