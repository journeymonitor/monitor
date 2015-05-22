<?php

use Phpmig\Migration\Migration;

class AddFailImageUrlFieldToTestresultsTable extends Migration
{
    public function up()
    {
        $sql = "ALTER TABLE testresults ADD COLUMN failScreenshotFilename TEXT DEFAULT NULL";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }

    public function down()
    {

    }
}
