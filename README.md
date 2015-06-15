# JourneyMonitor

## About this repository

Application that powers the monitoring backend of http://journeymonitor.com.

[![Build Status](https://travis-ci.org/journeymonitor/monitor.png?branch=master)](https://travis-ci.org/journeymonitor/monitor)


## About the JourneyMonitor project

Please see [ABOUT.md](https://github.com/journeymonitor/infra/blob/master/ABOUT.md) for more information.


## Setting up a development environment

### Using Vagrant (recommended)

Set up a development VM as described [in this document](https://github.com/journeymonitor/infra/blob/master/README.md#setting-up-a-development-environment).

Afterwards, follow these steps:

- SSH into the development VM by running `vagrant ssh` from the *infra* folder
- `cd /opt/journeymonitor/monitor`
- `make dependencies`
- `make migrations`

Thanks to https://github.com/journeymonitor/infra/blob/master/puppet/modules/cronjobs/templates/etc/cron.d/journeymonitor-monitor.erb#L8,
the testcases you add at http://192.168.99.99/ will be executed regularly.
