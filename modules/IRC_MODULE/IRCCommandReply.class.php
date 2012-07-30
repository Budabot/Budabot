<?php

//require_once 'CommandReply.class.php';

class IRCCommandReply implements CommandReply {
	private $chatBot;
	
	/** @Inject */
	public $setting;

	public function __construct(Budabot $chatBot) {
		$this->chatBot = $chatBot;
	}

	public function reply($msg) {
		global $ircSocket;
		IRC::send($ircSocket, $this->setting->get('irc_channel'), strip_tags($msg));
	}
}

?>
