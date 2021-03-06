<?php

namespace JourneyMonitor\Monitor\Base;

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
        $stmt->bindValue(':datetimeRun', $testresultModel->getDatetimeRun()->format('Y-m-d H:i:s')); # @TODO: We need the timezone here, too
        $stmt->bindValue(':exitCode', $testresultModel->getExitCode());
        $stmt->bindValue(':output', implode("\n", $testresultModel->getOutput()));
        $stmt->bindValue(':failScreenshotFilename', $testresultModel->getFailScreenshotFilename());
        $stmt->bindValue(':har', $testresultModel->getHar());

        $success = $stmt->execute();

        if (!$success) {
            print('Error while persisting ' . $testresultModel->getId() . ':' . print_r($stmt->errorInfo(), true) . "\n");
        }
    }

    public function getAll() {
        $results = [];
        $sql = 'SELECT id, testcaseId, datetimeRun, exitCode, output, failScreenshotFilename, har FROM testresults';
        foreach ($this->dbConnection->query($sql) as $row) {
            $results[] = $this->arrayToTestresultModel($row);
        }
        return $results;
    }

    public function getIteratorForAllSince(\DateTimeInterface $datetime) {
        $sql = 'SELECT id, testcaseId, datetimeRun, exitCode, output, failScreenshotFilename, har FROM testresults WHERE datetimeRun > ?';
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->execute([$datetime->format('Y-m-d H:i:s')]);
        return new TestresultModelIterator($this, $stmt);
    }

    public function getAllSince(\DateTimeInterface $datetime) {
        $results = [];
        $sql = 'SELECT id, testcaseId, datetimeRun, exitCode, output, failScreenshotFilename, har FROM testresults WHERE datetimeRun > "'.$datetime->format('Y-m-d H:i:s').'"';
        foreach ($this->dbConnection->query($sql) as $row) {
            $results[] = $this->arrayToTestresultModel($row);
        }
        return $results;
    }

    public function getNLastTestresultsForTestcase($n, TestcaseModel $testcase)
    {
        $results = [];
        $sql = 'SELECT id, testcaseId, datetimeRun, exitCode, output, failScreenshotFilename, har FROM testresults WHERE testcaseId = "' . $testcase->getId() . '" ORDER BY datetimeRun DESC LIMIT ' . (int)$n;
        foreach ($this->dbConnection->query($sql) as $row) {
            $results[] = $this->arrayToTestresultModel($row);
        }
        return $results;
    }

    public function removeAll()
    {
        $sql = 'DELETE FROM testresults';
        $this->dbConnection->exec($sql);
    }

    public function arrayToTestresultModel(array $row) {
        $testcaseModel = $this->testcaseRepository->getById($row['testcaseId']);
        if ($testcaseModel === null) {
            throw new \Exception();
        }
        return new TestresultModel(
            $row['id'],
            $testcaseModel,
            new \DateTime($row['datetimeRun']),
            $row['exitCode'],
            explode("\n", $row['output']),
            $row['failScreenshotFilename'],
            $row['har']
        );
    }

    private function remove(TestresultModel $testresultModel)
    {
        $sql = 'DELETE FROM testresults WHERE id = :id';
        $stmt = $this->dbConnection->prepare($sql);

        $stmt->bindValue(':id', $testresultModel->getId());

        $stmt->execute();
    }
}
