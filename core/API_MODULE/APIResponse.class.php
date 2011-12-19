<?php

define('API_SUCCESS', 1);
define('API_FAILURE', 0);

class APIResponse {
	public $status;
	public $message;
	public $syncId;

	function __construct($status, $message) {
		$this->status = $status;
		$this->message = $message;
	}
}

?>