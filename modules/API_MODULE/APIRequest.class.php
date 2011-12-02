<?php

class APIRequest {
	public $username;
	public $password;
	public $command;
	public $args;

	function __construct($username, $password, $command, $args) {
		$this->username = $username;
		$this->password = $password;
		$this->command = $command;
		$this->args = $args;
	}
}

?>