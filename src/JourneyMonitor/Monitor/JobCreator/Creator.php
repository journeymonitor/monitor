<?php

namespace JourneyMonitor\Monitor\JobCreator;

use JourneyMonitor\Monitor\Base\Logger;

class Creator
{
    private $testcaseRepository;
    private $directory;
    private $environmentName;
    private $logger;
    
    public function __construct($testcaseRepository, $directory, $environmentName, Logger $logger)
    {
        $this->testcaseRepository = $testcaseRepository;
        $this->directory = $directory;
        $this->environmentName = $environmentName;
        $this->logger = $logger;
    }
    
    public function run()
    {
        $testcaseModels = $this->testcaseRepository->getAll();

        if ($testcaseModels === false) {
            $this->logger->info('Problem while fetching testcases, aborting.');
            return false;
        }

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
                    ' | while IFS= read -r line;do echo "$(date) $line";done >> /var/tmp/journeymonitor-run-testcase-' . $testcaseModel->getId() . '-cronjob.log 2>&1' .
                    "\n"
            );
        }

        return true;
    }
}
