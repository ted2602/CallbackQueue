#!/bin/bash

SCRIPT=qc-service.php

# Check to see if a script by this name is running
PIDS=$(pgrep -f $SCRIPT)

if [[ "$PIDS" ]]; then
	echo "Daemon Running ($PIDS)"
	exit 0
else
	echo "Daemon Not Running (Restarted)"
	/var/www/html/admin/modules/callbackqueue/start-queuecallback.sh &> /dev/null &
	exit 1
fi


