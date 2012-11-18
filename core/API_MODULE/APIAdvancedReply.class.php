<?php

class APIAdvancedReply implements CommandReply {
	private $output;

	public function reply($message) {
		$this->output = $message;
	}

	public function getOutput() {
		return $this->output;
	}
}

?>
