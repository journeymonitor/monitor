<?php

namespace Selenior\Monitor\JobCreator;

use Selenior\Monitor\Base\TestcaseRepository;

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
                $this->directory . DIRECTORY_SEPARATOR . 'selenior-run-testcase-'.$testcaseModel->getId(),
                $testcaseModel->getCadence() .
                    ' * * * * root cd /tmp && export PHP_ENV=' .
                    $this->environmentName .
                    ' && sudo -u selenior -H /usr/bin/php /opt/selenior/monitor/bin/run.php ' .
                    $testcaseModel->getId() .
                    ' >> /var/tmp/selenior-run-testcase-' . $testcaseModel->getId() . '-cronjob.log 2>&1' .
                    "\n"
            );
        }
    }
}
