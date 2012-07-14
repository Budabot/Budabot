<?php

require_once 'CommandReply.class.php';

class GuildChannelCommandReply implements CommandReply {
	private $chatBot;

	public function __construct(Budabot $chatBot) {
		$this->chatBot = $chatBot;
	}

	public function reply($msg) {
		$this->chatBot->sendGuild($msg);
	}
}

?>
