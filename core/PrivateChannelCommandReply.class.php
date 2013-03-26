<?php

namespace Budabot\Core;

require_once 'CommandReply.class.php';

class PrivateChannelCommandReply implements CommandReply {
	private $chatBot;
	private $channel;

	public function __construct(Budabot $chatBot, $channel) {
		$this->chatBot = $chatBot;
		$this->channel = $channel;
	}

	public function reply($msg) {
		$this->chatBot->sendPrivate($msg, false, $this->channel);
	}
}

?>
