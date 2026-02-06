# API FILES TO UPLOAD TO HOSTINGER

## REQUIRED PHP FILES (keep):
- login.php
- register.php
- verify-otp.php
- entries.php
- play.php
- results.php
- upcoming-draws.php
- past-draws.php
- winners.php
- draws.php
- stats.php
- dashboard-stats.php
- users.php
- analytics.php
- analytics-data.php
- admin-upload-result.php
- admin-delete-result.php
- delete-draw.php
- announce-winners.php
- send-notification.php
- mark-paid.php
- notifications.php
- get-draw-info.php
- .htaccess
- index.php

## REQUIRED CONFIG FILES (keep):
- config/database.php
- config/jwt.php
- config/cors.php
- config/otp.php
- config/validator.php
- config/ratelimit.php

## REQUIRED ENV FILES (keep):
- .env
- .env.production

## REQUIRED HTML FILES (keep):
- privacy.html
- unsubscribe.html

## SQL FILES TO DELETE (migration only, not for production):
- align-columns.sql
- column-comparison.sql
- migration-update.sql
- rename-tables-final.sql
- rename-tables-to-singular.sql
- rollback-to-plural.sql
- setup-database-singular.sql
- setup-database.sql

## SQL FILES TO RUN ON HOSTINGER:
1. hostinger-setup.sql - Run this FIRST to create the draws view

After running, you can delete all .sql files from production

## UNUSED PHP FILES TO DELETE:
- admin-upload-result-enhanced.php (duplicate)
- activity-logs.php (not used)
- api-keys.php (not used)
- bulk-actions.php (not used)
- pool.php (not used)
- process-winners.php (not used)
- resend-otp.php (not used)
- site-settings.php (not used)
- verify-email.php (not used)
