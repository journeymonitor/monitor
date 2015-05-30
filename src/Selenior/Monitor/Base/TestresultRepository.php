<?php

namespace Selenior\Monitor\Base;

class TestresultRepository
{
    private $dbConnection;
    private $testcaseRepository;

    public function __construct(\PDO $dbConnection, TestcaseRepository $testcaseRepository)
    {
        $this->dbConnection = $dbConnection;
        $this->testcaseRepository = $testcaseRepository;
    }

    public function add(TestresultModel $testresultModel)
    {
        $sql = 'INSERT INTO testresults (id, testcaseId, datetimeRun, exitCode, output, failScreenshotFilename, har) VALUES (:id, :testcaseId, :datetimeRun, :exitCode, :output, :failScreenshotFilename, :har)';
        $stmt = $this->dbConnection->prepare($sql);

        $stmt->bindValue(':id', $testresultModel->getId());
        $stmt->bindValue(':testcaseId', $testresultModel->getTestcase()->getId());
        $stmt->bindValue(':datetimeRun', $testresultModel->getDatetimeRun()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':exitCode', $testresultModel->getExitCode());
        $stmt->bindValue(':output', implode("\n", $testresultModel->getOutput()));
        $stmt->bindValue(':failScreenshotFilename', $testresultModel->getFailScreenshotFilename());
        $stmt->bindValue(':har', $testresultModel->getHar());

        $stmt->execute();
    }

    public function getAll() {
        $results = [];
        $sql = 'SELECT id, testcaseId, datetimeRun, exitCode, output, failScreenshotFilename, har FROM testresults';
        foreach ($this->dbConnection->query($sql) as $row) {
            $results[] = $this->rowToTestresultModel($row);
        }
        return $results;
    }

    public function getAllSince(\DateTimeInterface $datetime) {
        $results = [];
        $sql = 'SELECT id, testcaseId, datetimeRun, exitCode, output, failScreenshotFilename, har FROM testresults WHERE datetimeRun > "'.$datetime->format('Y-m-d H:i:s').'"';
        foreach ($this->dbConnection->query($sql) as $row) {
            $results[] = $this->rowToTestresultModel($row);
        }
        return $results;
    }

    public function removeAll()
    {
        $sql = 'DELETE FROM testresults';
        $this->dbConnection->exec($sql);
    }

    private function remove(TestresultModel $testresultModel)
    {
        $sql = 'DELETE FROM testresults WHERE id = :id';
        $stmt = $this->dbConnection->prepare($sql);

        $stmt->bindValue(':id', $testresultModel->getId());

        $stmt->execute();
    }

    private function rowToTestresultModel($row) {
        return new TestresultModel(
            $row['id'],
            $this->testcaseRepository->getById($row['testcaseId']),
            new \DateTime($row['datetimeRun']),
            $row['exitCode'],
            explode("\n", $row['output']),
            $row['failScreenshotFilename'],
            $row['har']
        );
    }
}
