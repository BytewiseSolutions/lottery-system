#!/bin/bash
cd "$(dirname "$0")"
php dev-scheduler.php > /tmp/lottery-scheduler.log 2>&1 &
echo $! > /tmp/lottery-scheduler.pid
echo "Scheduler started with PID $(cat /tmp/lottery-scheduler.pid)"
echo "Logs: tail -f /tmp/lottery-scheduler.log"
