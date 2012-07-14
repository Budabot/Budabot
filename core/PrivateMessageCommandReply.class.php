<?php

require_once 'CommandReply.class.php';

class PrivateMessageCommandReply implements CommandReply {
	private $chatBot;
	private $sender;

	public function __construct(Budabot $chatBot, $sender) {
		$this->chatBot = $chatBot;
		$this->sender = $sender;
	}

	public function reply($msg) {
		$this->chatBot->sendTell($msg, $this->sender);
	}
}

?>
