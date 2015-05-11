<?php

namespace Selenior\Monitor\Base;

class TestcaseRepository
{
    private $dbConnection;

    public function __construct(\PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
        $dbConnection->exec("CREATE TABLE IF NOT EXISTS testcases (
                             id TEXT PRIMARY KEY,
                             userId TEXT,
                             cadence TEXT,
                             script TEXT)");
    }

    public function add(TestcaseModel $testcaseModel)
    {
        $this->remove($testcaseModel);
        $sql = 'INSERT INTO testcases (id, userId, cadence, script) VALUES (:id, :userId, :cadence, :script)';
        $stmt = $this->dbConnection->prepare($sql);

        $stmt->bindValue(':id', $testcaseModel->getId());
        $stmt->bindValue(':userId', $testcaseModel->getUserId());
        $stmt->bindValue(':cadence', $testcaseModel->getCadence());
        $stmt->bindValue(':script', $testcaseModel->getScript());

        $stmt->execute();
    }

    public function getAll() {
        $results = [];
        $sql = 'SELECT id, userId, cadence, script FROM testcases';
        $rows = $this->dbConnection->query($sql)->fetch(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $results[] = new TestcaseModel($row['id'], $row['userId'], $row['cadence'], $row['script']);
        }
        return $results;
    }

    private function remove(TestcaseModel $testcaseModel)
    {
        $sql = 'DELETE FROM testcases WHERE id = :id';
        $stmt = $this->dbConnection->prepare($sql);

        $stmt->bindValue(':id', $testcaseModel->getId());

        $stmt->execute();
    }
}
