<?php

namespace JourneyMonitor\Monitor\JobCreator;

class Creator
{
    private $testcaseRepository;
    private $directory;
    private $environmentName;
    
    public function __construct($testcaseRepository, $directory, $environmentName)
    {
        $this->testcaseRepository = $testcaseRepository;
        $this->directory = $directory;
        $this->environmentName = $environmentName;
    }
    
    public function run()
    {
        $testcaseModels = $this->testcaseRepository->getAll();
        foreach ($testcaseModels as $testcaseModel) {
            file_put_contents(
                $this->directory . DIRECTORY_SEPARATOR . 'journeymonitor-run-testcase-'.$testcaseModel->getId(),
                'MAILTO=""' .
                    "\n" .
                    '# ' .
                    $testcaseModel->getTitle() .
                    "\n" .
                    $testcaseModel->getCadence() .
                    ' * * * * root cd /tmp && sudo -u journeymonitor -H PHP_ENV=' .
                    $this->environmentName .
                    ' /usr/bin/php /opt/journeymonitor/monitor/bin/run.php ' .
                    $testcaseModel->getId() .
                    ' >> /var/tmp/journeymonitor-run-testcase-' . $testcaseModel->getId() . '-cronjob.log 2>&1' .
                    "\n"
            );
        }
    }
}
