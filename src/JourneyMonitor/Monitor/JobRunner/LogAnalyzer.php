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

    /**
     * Get URLs of the pages that where visited during the journey
     *
     * This retrieves a list of the "main" web pages that were requested during the testcase run. The "main" pages are
     * those that the Selenium script navigated to, not secondary HTTP requests which resulted from these page loads.
     *
     * @param string $logContent
     * @return Array|String Array of requested URLs
     */
    public function getUrlsOfRequestedPages($logContent)
    {
        // [2015-05-28 23:46:34.933 +02:00] [INFO] - [Success] URL: [https://www.galeria-kaufhof.de/search?q=hose&page=4] / Title: [Suchergebnis f?r hose | GALERIA Kaufhof]
        $matches = [];
        $urls = [];
        $lines = explode("\n", $logContent);
        foreach ($lines as $line) {
            if (strstr($line, '] [INFO] - [Success] URL: [')) {
                preg_match('/\] \[INFO\] - \[Success\] URL: \[(.*?)\]/', $line, $matches);
                if (array_key_exists(1, $matches)) {
                    $urls[] = $matches[1];
                }
            }
        }
        return $urls;
    }

}
