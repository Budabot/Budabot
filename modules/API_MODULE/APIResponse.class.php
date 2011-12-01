<?php

define(SUCCESS, 1);
define(FAILURE, 0);

class APIResponse {
	public $status;
	public $message;
	public $output;

	function __construct($status, $message, $output) {
		$this->status = $status;
		$this->message = $message;
		$this->output = $output;
	}
}

?>