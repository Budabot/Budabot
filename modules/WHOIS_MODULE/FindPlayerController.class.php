<?php

namespace Budabot\User\Modules;

use Budabot\Core\DB;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'findplayer',
 *		accessLevel = 'all', 
 *		description = 'Find a player by name', 
 *		help        = 'findplayer.txt'
 *	)
 */
class FindPlayerController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $playerManager;
	
	/**
	 * @HandlesCommand("findplayer")
	 * @Matches("/^findplayer (.+)$/i")
	 */
	public function findplayerCommand($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];
		
		// wild cards
		$searchTerm = str_replace("*", "%", $search);
		$searchTerm = str_replace("?", "_", $searchTerm);
		
		$data = $this->playerManager->searchForPlayers('%' . $searchTerm . '%', $this->chatBot->vars['dimension']);
		$count = count($data);

		if ($count > 0) {
			$blob = '';
			forEach ($data as $row) {
				$blob .= $this->playerManager->get_info($row, false) . "\n\n";
			}
			$msg = $this->text->make_blob("Search results for '$search' ($count)", $blob);
		} else {
			$msg = "Could not find any players matching <highlight>$search<end>.";
		}

		$sendto->reply($msg);
	}
}
