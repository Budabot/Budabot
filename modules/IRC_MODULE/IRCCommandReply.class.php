<?php

class IRCCommandReply implements CommandReply {
	private $irc;
	private $channel;
	private $type;

	public function __construct(&$irc, $channel, $type) {
		$this->irc = $irc;
		$this->channel = $channel;
		$this->type = $type;
	}

	public function reply($msg) {
		$this->irc->message($this->type, $this->channel, strip_tags($msg));
	}
}