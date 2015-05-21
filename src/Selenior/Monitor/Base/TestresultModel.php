<?php

namespace Selenior\Monitor\Base;

class TestresultModel
{
    private $testcase;
    private $datetimeRun;
    private $exitCode;
    private $output;

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

    public function __construct($id, TestcaseModel $testcase, $datetimeRun, $exitCode, $output)
    {
        $this->id = $id;
        $this->testcase = $testcase;
        $this->datetimeRun = $datetimeRun;
        $this->exitCode = $exitCode;
        $this->output = $output;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTestcase()
    {
        return $this->testcase;
    }

    public function getDatetimeRun()
    {
        return $this->datetimeRun;
    }

    public function getExitCode()
    {
        return $this->exitCode;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
