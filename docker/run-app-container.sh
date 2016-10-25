#!/usr/bin/env bash

# The full path to this script, no matter where it is called from
SCRIPTDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

docker stop journeymonitor-monitor 2> /dev/null
docker rm journeymonitor-monitor 2> /dev/null

docker run -d \
    -v $SCRIPTDIR/../:/opt/journeymonitor/monitor \
    -v $SCRIPTDIR/assets:/root/docker-assets \
    -v $SCRIPTDIR/../../infra:/root/docker-assets/infra \
    -p 9080:80 \
    -p 9081:8081 \
    -p 9083:8083 \
    --net journeymonitor \
    --name journeymonitor-monitor \
    journeymonitor/monitor:latest /bin/bash /root/docker-assets/boot-application.sh
