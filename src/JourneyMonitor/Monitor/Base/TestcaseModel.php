<?php

namespace JourneyMonitor\Monitor\Base;

class TestcaseModel
{
    private $id;
    private $title;
    private $notifyEmail;
    private $cadence;
    private $script;

    public function __construct($id, $title, $notifyEmail, $cadence, $script) {
        $this->id = $id;
        $this->title = $title;
        $this->notifyEmail = $notifyEmail;
        $this->cadence = $cadence;
        $this->script = $script;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
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
