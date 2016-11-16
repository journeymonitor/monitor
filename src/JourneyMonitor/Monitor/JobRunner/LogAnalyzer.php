<?php

namespace JourneyMonitor\Monitor\JobRunner;

class LogAnalyzer
{
    public function lineContainsScreenshot($line)
    {
        return (bool)strstr($line, '[INFO] - captured screenshot: ');
    }

    public function getScreenshotFilenameFromLine($line)
    {
        if (!$this->lineContainsScreenshot($line)) {
            return false;
        } else {
            return substr($line, strpos($line, '[INFO] - captured screenshot: ') + 66);
        }
    }
    
    public function pageloadTimeoutOccured($outputLines) {
        foreach ($outputLines as $outputLine) {
            if (preg_match('/^\[(.*?)\] \[ERROR\] (.*?) \[Error: TimeoutException - Timed out waiting for page load.$/', $outputLine)) {
                return true;
            }
            if (preg_match('/^\[(.*?)\] \[ERROR\] (.*?) \[Failure: Timed out waiting for page load.(.*?)$/', $outputLine)) {
                return true;
            }
        }
        return false;
    }
}
