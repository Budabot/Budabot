<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'runas', 
 *		accessLevel = 'superadmin', 
 *		description = 'Execute a command as another character', 
 *		help        = 'runas.txt'
 *	)
 */
class RunAsController extends AutoInject {

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
	 * @HandlesCommand("runas")
	 * @Matches("/^runas ([a-z0-9-]+) (.+)$/i")
	 */
	public function runasCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$command = $args[2];
		$this->commandManager->process($channel, $command, $name, $sendto);
	}
}
