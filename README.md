# JourneyMonitor

## About this repository

Application that powers the monitoring backend of http://journeymonitor.com.

[![Build Status](https://travis-ci.org/journeymonitor/monitor.png?branch=master)](https://travis-ci.org/journeymonitor/monitor)


## About the JourneyMonitor project

Please see [ABOUT.md](https://github.com/journeymonitor/infra/blob/master/ABOUT.md) for more information.


## Setting up a development environment

### Using Docker (recommended)

Set up a development environment as described [in this document](https://github.com/journeymonitor/infra/blob/master/README.md#setting-up-a-development-environment).

Afterwards, follow these steps:

- From `infra/docker`, run `docker-compose exec journeymonitor-monitor bash`
- `cd /opt/journeymonitor/monitor`

Enjoy. Consider running most command via `sudo -u www-data`, because `www-data` is the owner of all files in
/opt/journeymonitor/monitor and the owner of the nginx and php-fpm processes.

Example:
- `cd /opt/journeymonitor/control`
- `sudo -u www-data make test`

Thanks to https://github.com/journeymonitor/infra/blob/master/puppet/modules/cronjobs/templates/etc/cron.d/journeymonitor-monitor.erb#L8,
the testcases you add through the web UI will be executed regularly.
