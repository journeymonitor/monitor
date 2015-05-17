<?php

namespace Selenior\Monitor\JobRunnor;

class TestresultModel
{
    private $testcase;
    private $datetimeRun;
    private $exitCode;
    private $output;

    public function __construct($testcase, $datetimeRun, $exitCode, $output)
    {
        $this->testcase = $testcase;
        $this->datetimeRun = $datetimeRun;
        $this->exitCode = $exitCode;
        $this->output = $output;
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
