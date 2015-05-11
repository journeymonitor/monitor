<?php

namespace Selenior\Monitor\Base;

class TestcaseModel {

    private $id;
    private $userId;
    private $cadence;
    private $script;

    public function __construct($id, $userId, $cadence, $script) {
        $this->id = $id;
        $this->userId = $userId;
        $this->cadence = $cadence;
        $this->script = $script;
    }

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getCadence() {
        return $this->cadence;
    }

    public function getScript() {
        return $this->script;
    }

    public function setScript($script) {
        $this->script = $script;
    }

}
