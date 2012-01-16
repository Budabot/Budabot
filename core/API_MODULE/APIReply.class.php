<?php

class APIReply implements CommandReply {
	private $output = array();

	public function reply($msg) {
		$this->output []= $message;
	}
	
	public function getOutput() {
		return implode("\n", $this->output);
	}
}

?>