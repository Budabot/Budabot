<?php

class LoggerAppenderBuffer extends LoggerAppender {

	private $logBuffer;
	private $logLimit;
	private $emitter;

	public function __construct($name = '') {
		parent::__construct($name);
		$this->logBuffer = array();
		$this->logLimit = 50;
		$this->emitter = new Evenement\EventEmitter();
	}

	public function setLogLimit($value) {
		$this->logLimit = intval($value);
	}

	public function getLogLimit() {
		return $this->logLimit;
	}

	public function onEvent($callback) {
		return $this->emitter->on('event', $callback);
	}

	public function append(LoggerLoggingEvent $event) {
		if ($this->layout === null) {
			return;
		}

		$msg = $this->layout->format($event);

		$this->logBuffer []= $msg;
		$this->emitter->emit('event', array($msg));

		if (count($this->logBuffer) > $this->logLimit) {
			array_shift($this->logBuffer);
		}
	}
}

