<?php

use Phpmig\Migration\Migration;

class AddHarFieldToTestresultsTable extends Migration
{
    public function up()
    {
        $sql = "ALTER TABLE testresults ADD COLUMN har TEXT DEFAULT NULL";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }

    public function down()
    {

    }
}
