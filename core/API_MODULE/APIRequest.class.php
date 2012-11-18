<?php

define('API_SIMPLE_MSG', 0);
define('API_ADVANCED_MSG', 1);
define('API_VERSION', '1.2'); // this must be a string for comparison to work properly

class APIRequest {
	public $version = API_VERSION;
	public $username;
	public $password;
	public $command;
	public $type;
	public $syncId;

	function __construct($username, $password, $command, $type = API_SIMPLE_MSG, $syncId = 0) {
		$this->username = $username;
		$this->password = $password;
		$this->command = $command;
		$this->type = $type;
		$this->syncId = $syncId;
	}
}

?>