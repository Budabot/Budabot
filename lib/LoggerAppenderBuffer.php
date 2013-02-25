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

	public function getEvents() {
		return $this->logBuffer;
	}

	public function onEvent($callback) {
		return $this->emitter->on('event', $callback);
	}

	public function append(LoggerLoggingEvent $event) {
		$log = new StdClass();
		$log->time   = $event->getTimeStamp();
		$log->level  = $event->getLevel()->toString();
		$log->msg    = $event->getMessage();
		$log->logger = $event->getLoggerName();

		$this->logBuffer []= $log;
		$this->emitter->emit('event', array($log));

		if (count($this->logBuffer) > $this->logLimit) {
			array_shift($this->logBuffer);
		}
	}
}

