<?php

namespace Budabot\User\Modules;

use Budabot\Core\AutoInject;

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'sendtell', 
 *		accessLevel = 'superadmin', 
 *		description = 'Send a tell to another character from the bot', 
 *		help        = 'sendtell.txt'
 *	)
 */
class SendTellController extends AutoInject {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/**
	 * @Setup
	 */
	public function setup() {
		
	}
	
	/**
	 * @HandlesCommand("sendtell")
	 * @Matches("/^sendtell ([a-z0-9-]+) (.+)$/i")
	 */
	public function sendtellCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$message = $args[2];
		
		$this->logger->logChat("Out. Msg.", $name, $message);
		$this->chatBot->send_tell($name, $message, "\0", AOC_PRIORITY_MED);
		$sendto->reply("Message has been sent to <highlight>$name<end>.");
	}
}
