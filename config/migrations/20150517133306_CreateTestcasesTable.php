<?php

use Phpmig\Migration\Migration;

class CreateTestcasesTable extends Migration
{
    public function up()
    {
        $sql = "CREATE TABLE testcases (
                             id TEXT PRIMARY KEY,
                             title TEXT,
                             notifyEmail TEXT,
                             cadence TEXT,
                             script TEXT)";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }

    public function down()
    {
        $sql = "DROP TABLE testcases";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }
}
