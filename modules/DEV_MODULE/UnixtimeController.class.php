<?php

namespace Budabot\User\Modules;

use Budabot\Core\AutoInject;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'unixtime',
 *		accessLevel = 'all',
 *		description = 'Show the date and time for a unix timestamp',
 *		help        = 'unixtime.txt'
 *	)
 */
class UnixtimeController extends AutoInject {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/**
	 * @HandlesCommand("unixtime")
	 * @Matches("/^unixtime (\d+)$/i")
	 */
	public function reloadinstanceAllCommand($message, $channel, $sender, $sendto, $args) {
		$time = $args[1];
		
		$msg = "$time is " . $this->util->date($time) . ".";
		$sendto->reply($msg);
	}
}
