#!/bin/bash

/usr/bin/java \
    -jar /opt/selenese-runner-java/selenese-runner.jar \
    --driver phantomjs \
    --width 1280 \
    --height 720 \
    --screenshot-on-fail /var/tmp/selenior-screenshots \
    --strict-exit-code \
    $2
STATUS=$?

echo $STATUS > /var/tmp/selenior-testcase-run-$1-exit-status
