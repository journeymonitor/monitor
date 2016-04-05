<?php

use Phpmig\Migration\Migration;

class AddIndexOnDatetimeRunForTestresultsTable extends Migration
{
    public function up()
    {
        $sql = "CREATE INDEX idx_datetime_run ON testresults (datetimeRun);";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }

    public function down()
    {

    }
}
