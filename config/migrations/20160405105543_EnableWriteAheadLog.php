<?php

use Phpmig\Migration\Migration;

class EnableWriteAheadLog extends Migration
{
    public function up()
    {
        $sql = "PRAGMA journal_mode=WAL;";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }

    public function down()
    {

    }
}
