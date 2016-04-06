<?php

namespace JourneyMonitor\Monitor\Base;

class TestcaseRepository
{
    private $dbConnection;
    private $logger;

    public function __construct(\PDO $dbConnection, Logger $logger)
    {
        $this->dbConnection = $dbConnection;
        $this->logger = $logger;
    }

    public function getById($id)
    {
        $sql = 'SELECT id, title, notifyEmail, cadence, script FROM testcases WHERE id = :id';
        $stmt = $this->dbConnection->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            return new TestcaseModel($row['id'], $row['title'], $row['notifyEmail'], $row['cadence'], $row['script']);
        }
        return null;
    }
    
    public function add(TestcaseModel $testcaseModel)
    {
        $this->remove($testcaseModel);
        $sql = 'INSERT INTO testcases (id, title, notifyEmail, cadence, script) VALUES (:id, :title, :notifyEmail, :cadence, :script)';
        $stmt = $this->dbConnection->prepare($sql);

        $stmt->bindValue(':id', $testcaseModel->getId());
        $stmt->bindValue(':title', $testcaseModel->getTitle());
        $stmt->bindValue(':notifyEmail', $testcaseModel->getNotifyEmail());
        $stmt->bindValue(':cadence', $testcaseModel->getCadence());
        $stmt->bindValue(':script', $testcaseModel->getScript());

        $stmt->execute();
    }

    /**
     * @return array|bool Array of Testcases on success, false on error
     */
    public function getAll() {
        $results = [];
        $sql = 'SELECT id, title, notifyEmail, cadence, script FROM testcases';

        $rows = $this->dbConnection->query($sql);
        if ($rows === false) {
            $this->logger->info('Error while running query "' . $sql . '" ');
            $this->logger->info('Connection error info: "' . print_r($this->dbConnection->errorInfo(), true) . '" ');
            return false;
        } else {
            foreach ($rows as $row) {
                $results[] = new TestcaseModel($row['id'], $row['title'], $row['notifyEmail'], $row['cadence'], $row['script']);
            }
        }

        return $results;
    }

    public function removeAll()
    {
        $sql = 'DELETE FROM testcases';
        $this->dbConnection->exec($sql);
    }

    private function remove(TestcaseModel $testcaseModel)
    {
        $sql = 'DELETE FROM testcases WHERE id = :id';
        $stmt = $this->dbConnection->prepare($sql);

        $stmt->bindValue(':id', $testcaseModel->getId());

        $stmt->execute();
    }
}
