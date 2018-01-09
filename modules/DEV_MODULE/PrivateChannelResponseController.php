<?php

namespace Tyrence\Modules;

use Budabot\Core\CommandReply;

/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'g', 
 *		accessLevel = 'all', 
 *		description = 'Execute a command so that links will execute in private channel', 
 *		help        = 'g.txt'
 *	)
 */
class PrivateChannelResponseController {
	
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
	 * This command handler execute multiple commands at once.
	 *
	 * @HandlesCommand("g")
	 * @Matches("/^g (.+)$/si")
	 */
	public function gCommand($message, $channel, $sender, $sendto, $args) {
		$commandString = $args[1];
		$customSendto = new PrivateChannelResponseCommandReply($channel, $sendto, $this->chatBot->vars["name"]);
		$this->commandManager->process($channel, $commandString, $sender, $customSendto);
	}
}

class PrivateChannelResponseCommandReply implements CommandReply {
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
			$msg = str_replace("chatcmd:///tell {$this->botname} ", "chatcmd:///g {$this->botname} <symbol>", $msg);
		}
		$this->sendto->reply($msg);
	}
}
