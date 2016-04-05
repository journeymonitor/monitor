<?php

use Phpmig\Migration\Migration;

class DisableWriteAheadLog extends Migration
{
    public function up()
    {
        $sql = "PRAGMA journal_mode=DELETE;";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }

    public function down()
    {

    }
}
