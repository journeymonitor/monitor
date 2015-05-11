<?php

namespace Selenior\Monitor\Base;

class TestcaseModel
{
    private $id;
    private $name;
    private $notifyEmail;
    private $cadence;
    private $script;

    public function __construct($id, $name, $notifyEmail, $cadence, $script) {
        $this->id = $id;
        $this->name = $name;
        $this->notifyEmail = $notifyEmail;
        $this->cadence = $cadence;
        $this->script = $script;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getNotifyEmail() {
        return $this->notifyEmail;
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
