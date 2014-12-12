<?php

namespace Budabot\Core;

class TimerEvent {
	public $time = 0;
	public $delay = 0;
	public $callback = null;
	public $args = array();

	public function __construct($time, $delay, $callback, $args) {
		$this->time = $time;
		$this->delay = $delay;
		$this->callback = $callback;
		$this->args = $args;
	}

	/**
	 * @internal
	 */
	public function callCallback() {
		call_user_func_array($this->callback, $this->args);
	}
}
