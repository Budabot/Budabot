<?php

class APISimpleReply implements CommandReply {
	private $output = array();

	public function reply($message) {
		// if it's a blob
		if (is_array($message)) {
			$this->output = array_merge($this->output, $message);
		} else {
			$this->output []= $message;
		}
	}

	public function getOutput() {
		return implode("\n\n", $this->output);
	}
}

?>
