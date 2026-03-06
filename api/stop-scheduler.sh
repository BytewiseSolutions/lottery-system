#!/bin/bash
if [ -f /tmp/lottery-scheduler.pid ]; then
    kill $(cat /tmp/lottery-scheduler.pid) 2>/dev/null
    rm /tmp/lottery-scheduler.pid
    echo "Scheduler stopped"
else
    echo "Scheduler is not running"
fi
