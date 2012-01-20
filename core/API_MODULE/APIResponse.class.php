<?php

define('API_SUCCESS', 0);
define('API_INVALID_VERSION', 1);
define('API_UNSET_PASSWORD', 2);
define('API_INVALID_PASSWORD', 3);
define('API_INVALID_REQUEST_TYPE', 4);
define('API_UNKNOWN_COMMAND', 5);
define('API_ACCESS_DENIED', 6);
define('API_SYNTAX_ERROR', 7);
define('API_EXCEPTION', 8);

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