<?php

use Phpmig\Migration\Migration;

class CreateTestresultsTable extends Migration
{

    public function up()
    {
        $sql = "CREATE TABLE testresults (
                             id TEXT PRIMARY KEY,
                             testcaseId TEXT,
                             datetimeRun DATETIME,
                             exitCode TINYINT,
                             output TEXT)";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE testresults";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }
}
