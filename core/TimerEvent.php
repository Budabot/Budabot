<?php

class TimerEvent {
	public $time = 0;
	private $delay = 0;
	private $callback = null;
	private $args = array();
	private $timer = null;

	public function __construct($timer, $delay, $callback, $args) {
		$this->delay = $delay;
		$this->timer = $timer;
		$this->callback = $callback;
		$this->args = $args;
		$this->resetTriggerTime();
	}

	/**
	 * This method aborts this timed event, unless it has already triggered.
	 */
	public function abort() {
		$this->timer->abortEvent( $this );
	}

	/**
	 * Restarts this timed event.
	 */
	public function restart() {
		$this->resetTriggerTime();
		$this->timer->restartEvent( $this );
	}

	/**
	 * @internal
	 */
	public function callCallback() {
		call_user_func_array($this->callback, $this->args);
	}

	private function resetTriggerTime() {
		$this->time = intval($this->delay) + time();
	}
}
