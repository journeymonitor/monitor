#!/bin/bash

/usr/bin/Xvfb :$1 -nolisten tcp -ac > /dev/null 2> /dev/null &
XVFB_PID=$!

export DISPLAY=:$1
mkdir /var/tmp/journeymonitor-firefox-profile-$XVFB_PID

/usr/bin/curl -s -X POST -d "port=$2" http://localhost:9090/proxy
/usr/bin/curl -s -X PUT -d "captureHeaders=1" http://localhost:9090/proxy/$2/har

/usr/bin/java \
    -jar /opt/selenese-runner-java/selenese-runner.jar \
    --driver firefox \
    --proxy localhost:$2 \
    --cli-args "--new-instance" \
    --cli-args "--profile /var/tmp/journeymonitor-firefox-profile-$XVFB_PID" \
    --width 1920 \
    --height 1200 \
    --screenshot-on-fail /var/tmp/journeymonitor-screenshots \
    --strict-exit-code \
    $3
STATUS=$?

echo $STATUS > /var/tmp/journeymonitor-testcase-run-$1-exit-status

/usr/bin/curl -s http://localhost:9090/proxy/$2/har > /var/tmp/journeymonitor-testcase-run-$1-har
/usr/bin/curl -s -X DELETE http://localhost:9090/proxy/$2

rm -rf /var/tmp/journeymonitor-firefox-profile-$XVFB_PID
kill $XVFB_PID
