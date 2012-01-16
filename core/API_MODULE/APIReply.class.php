<?php

class APIReply implements CommandReply {
	private $output = array();

	public function reply($message) {
		$this->output []= $message;
	}
	
	public function getOutput() {
		return implode("\n", $this->output);
	}
}

?>