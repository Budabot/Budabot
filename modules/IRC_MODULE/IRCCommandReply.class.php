<?php

class IRCCommandReply implements CommandReply {
	private $chatBot;
	private $ircSocket;
	private $channel;

	public function __construct(Budabot $chatBot, $ircSocket, $channel) {
		$this->chatBot = $chatBot;
		$this->ircSocket = $ircSocket;
		$this->channel = $channel;
	}

	public function reply($msg) {
		IRC::send($this->ircSocket, $this->channel, strip_tags($msg));
	}
}

?>
