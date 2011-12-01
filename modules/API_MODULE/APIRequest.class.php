<?php

class APIRequest {
	public $command;
	public $args;

	function __construct($command, $args) {
		$this->command = $command;
		$this->args = $args;
	}
}

?>