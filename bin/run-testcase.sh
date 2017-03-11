#!/bin/bash

/usr/bin/Xvfb :$1 -screen 0 1920x1200x24 -nolisten tcp -ac > /dev/null 2> /dev/null &
XVFB_PID=$!

export DISPLAY=:$1

# Launching a browsermob proxy, ensuring that we don't work with a port that is already taken
PROXY_STARTED=0
while [ $PROXY_STARTED -eq 0 ]
do
    PROXY_PORT=$(( ( RANDOM % 32767 ) + 9091 ))
    PROXY_START_OUTPUT=`/usr/bin/curl -s -X POST -d "port=$PROXY_PORT" http://localhost:9090/proxy`
    if [ "$PROXY_START_OUTPUT" == "{\"port\":$PROXY_PORT}" ]
    then
        PROXY_STARTED=1
    fi
done

/usr/bin/curl -s -X PUT -d "captureHeaders=1" http://localhost:9090/proxy/$PROXY_PORT/har

# Firefoxes should not be started in parallel it seems
sleep $[ ( $RANDOM % 10 ) + 1 ]s

/usr/bin/java \
    -jar /opt/selenese-runner-java/selenese-runner.jar \
    --driver firefox \
    --cli-args "--new-instance" \
    --proxy localhost:$PROXY_PORT \
    --no-proxy mozilla.com,mozilla.org,mozilla.net,digicert.com \
    --width 1920 \
    --height 1200 \
    --screenshot-on-fail /var/tmp/journeymonitor-screenshots \
    --strict-exit-code \
    --timeout 240000 \
    $2 2>&1
STATUS=$?

echo $STATUS > /var/tmp/journeymonitor-testcase-run-$1-exit-status

/usr/bin/curl -s http://localhost:9090/proxy/$PROXY_PORT/har > /var/tmp/journeymonitor-testcase-run-$1-har
/usr/bin/curl -s -X DELETE http://localhost:9090/proxy/$PROXY_PORT

kill $XVFB_PID
