<?php

namespace JourneyMonitor\Monitor\Base;

class TestresultModel
{
    private $testcase;
    private $datetimeRun;
    private $exitCode;
    private $output;
    private $failScreenshotFilename;
    private $har;

    public static function generateId() {
        return strtoupper(sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        ));
    }

    public function __construct($id, TestcaseModel $testcase, \DateTime $datetimeRun, $exitCode, Array $output, $failScreenshotFilename, $har)
    {
        $this->id = (string)$id;
        $this->testcase = $testcase;
        $this->datetimeRun = $datetimeRun;
        $this->exitCode = (int)$exitCode;
        $this->output = $output;
        $this->failScreenshotFilename = (string)$failScreenshotFilename;
        $this->har = (string)$har;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return TestcaseModel
     */
    public function getTestcase()
    {
        return $this->testcase;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeRun()
    {
        return $this->datetimeRun;
    }

    /**
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @return array
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return string
     */
    public function getFailscreenshotFilename()
    {
        return $this->failScreenshotFilename;
    }

    /**
     * @return string
     */
    public function getHar()
    {
        return $this->har;
    }
}
