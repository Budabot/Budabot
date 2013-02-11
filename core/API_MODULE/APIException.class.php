<?php

namespace budabot\core\modules;

use \Exception;

class APIException extends Exception {
	private $responseMessage;

	public function __construct($responseMessage) {
		$this->responseMessage = $responseMessage;
	}

	public function getResponseMessage() {
		return $this->responseMessage;
	}
}

?>
