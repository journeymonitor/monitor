#!/usr/bin/env bash

set -e

cd /opt/journeymonitor/monitor
composer install
sudo -u journeymonitor make migrations

/etc/init.d/rsyslog start
/usr/sbin/cron

JAVA_HOME=/usr/lib/jvm/java-7-openjdk-amd64 JRE_HOME=/usr/lib/jvm/java-7-openjdk-amd64/jre \
    /usr/bin/nohup /bin/bash /opt/browsermob-proxy-2.0.0/bin/browsermob-proxy \
        --address 127.0.0.1 \
        --port 9090 \
        --ttl 3600 \
    >> /var/log/browsermob-proxy.log 2>&1 &

/etc/init.d/php5-fpm start
/etc/init.d/nginx start

tail -f /dev/null # keep running
