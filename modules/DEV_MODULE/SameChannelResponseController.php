<?php

namespace Tyrence\Modules;

use Budabot\Core\CommandReply;

/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'demo', 
 *		accessLevel = 'all', 
 *		description = 'Execute a command so that links will execute in the same channel', 
 *		help        = 'demo.txt'
 *	)
 */
class SameChannelResponseController {
	
	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $commandManager;

	/** @Inject */
	public $chatBot;
	
	/**
	 * @HandlesCommand("demo")
	 * @Matches("/^demo (.+)$/si")
	 */
	public function demoCommand($message, $channel, $sender, $sendto, $args) {
		$commandString = $args[1];
		$customSendto = new DemoResponseCommandReply($channel, $sendto, $this->chatBot->vars["name"]);
		$this->commandManager->process($channel, $commandString, $sender, $customSendto);
	}
}

class DemoResponseCommandReply implements CommandReply {
	private $sendto;
	private $channel;
	private $botname;
	
	public function __construct($channel, $sendto, $botname) {
		$this->channel = $channel;
		$this->sendto = $sendto;
		$this->botname = $botname;
	}

	public function reply($msg) {
		if ($this->channel == 'priv') {
			$msg = str_replace("chatcmd:///tell {$this->botname} ", "chatcmd:///g {$this->botname} <symbol>demo ", $msg);
		} else if ($this->channel == 'guild') {
			$msg = str_replace("chatcmd:///tell {$this->botname} ", "chatcmd:///o <symbol>demo ", $msg);
		}
		$this->sendto->reply($msg);
	}
}
