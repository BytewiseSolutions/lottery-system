#!/bin/bash
# Cron job to automatically manage lottery draws
# Add this to your crontab: * * * * * /path/to/lottery-system/api/cron-manage-draws.sh

cd "$(dirname "$0")"
php fix-draws.php >> /tmp/lottery-cron.log 2>&1
