<?php

namespace budabot\core\modules;

use budabot\core\CommandReply;

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
