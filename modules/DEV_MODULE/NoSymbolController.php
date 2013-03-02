<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 */
class NoSymbolController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $commandManager;
	
	/** @Inject */
	public $setting;
	
	/**
	 * @Setup
	 */
	public function setup() {
		
	}
	
	/**
	 * @Event("guild")
	 * @Description("Parse commands from guild channel")
	 * @DefaultStatus("0")
	 */
	public function parseGuildCommandEvent($eventObj) {
		$this->parseCommand($eventObj->type, $eventObj->message, $eventObj->sender, new GuildChannelCommandReplyA($this->chatBot));
	}
	
	/**
	 * @Event("priv")
	 * @Description("Parse commands from private channel")
	 * @DefaultStatus("0")
	 */
	public function parsePrivateChannelCommandEvent($eventObj) {
		$this->parseCommand($eventObj->type, $eventObj->message, $eventObj->sender, new PrivateChannelCommandReplyA($this->chatBot, $this->chatBot->vars['name']));
	}
	
	public function parseCommand($type, $cmd, $sender, $sendto) {
		if ($message[0] != $this->setting->symbol) {
			$this->commandManager->process($type, $cmd, $sender, $sendto);
		}
	}
}

class GuildChannelCommandReplyA extends GuildChannelCommandReply {
	public function reply($msg) {
		if (!preg_match("/Error! Unknown command/", $msg)) {
			parent::reply($msg);
		}
	}
}

class PrivateChannelCommandReplyA extends PrivateChannelCommandReply {
	public function reply($msg) {
		if (!preg_match("/Error! Unknown command/", $msg)) {
			parent::reply($msg);
		}
	}
}

?>
