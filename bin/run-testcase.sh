#!/bin/bash

/usr/bin/Xvfb :$1 -nolisten tcp -ac > /dev/null 2> /dev/null &
XVFB_PID=$!

export DISPLAY=:$1
mkdir /var/tmp/selenior-firefox-profile-$XVFB_PID

/usr/bin/java \
    -jar /opt/selenese-runner-java/selenese-runner.jar \
    --driver firefox \
    --cli-args "--new-instance" \
    --cli-args "--profile /var/tmp/selenior-firefox-profile-$XVFB_PID" \
    --width 1920 \
    --height 1200 \
    --strict-exit-code \
    $2
STATUS=$?

echo $STATUS > /var/tmp/selenior-testcase-run-$1-exit-status

rm -rf /var/tmp/selenior-firefox-profile-$XVFB_PID
kill $XVFB_PID
