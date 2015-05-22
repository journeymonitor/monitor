#!/bin/bash

/usr/bin/java \
    -jar /opt/selenese-runner-java/selenese-runner.jar \
    --driver phantomjs \
    --width 1920 \
    --height 1200 \
    --strict-exit-code \
    $2
STATUS=$?

echo $STATUS > /var/tmp/selenior-testcase-run-$1-exit-status
