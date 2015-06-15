<?php

namespace JourneyMonitor\Monitor\JobRunnor;

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
            return substr($line, 92);
        }
    }
}
