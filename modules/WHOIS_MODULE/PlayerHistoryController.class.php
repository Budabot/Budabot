<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'history',
 *		accessLevel = 'all', 
 *		description = 'Show history of a player', 
 *		help        = 'history.txt'
 *	)
 */
class PlayerHistoryController {

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
	public $playerHistoryManager;

	/**
	 * @HandlesCommand("history")
	 * @Matches("/^history ([^ ]+) (\d)$/i")
	 * @Matches("/^history ([^ ]+)$/i")
	 */
	public function playerHistoryCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		if (count($args) == 3) {
			$dimension = $args[2];
		} else {
			$dimension = $this->chatBot->vars['dimension'];
		}

		$history = $this->playerHistoryManager->lookup($name, $dimension);
		if ($history === null) {
			$msg = "Could not get History of $name on RK$dimension.";
		} else {
			$blob = "Date           Level    AI     Faction      Guild(rank) \n";
			$blob .= "________________________________________________ \n";
			forEach ($history->data as $entry) {
				if ($entry->aiLevel == "") {
					$ailevel = "<green>0<end>";
				} else {
					$ailevel = "<green>$entry->aiLevel<end>";
				}

				if ($entry->faction == "Omni") {
					$faction = "<omni>Omni<end>";
				} else if ($entry->faction == "Clan") {
					$faction = "<clan>Clan<end>";
				} else {
					$faction = "<neutral>Neutral<end>";
				}

				if ($entry->guild == "") {
					$guild = "Not in a guild";
				} else {
					$guild = $entry->guild . "(" . $entry->rank . ")";
				}

				$blob .= "$entry->date |  $entry->level  | $ailevel | $faction | $guild\n";
			}
			$msg = $this->text->make_blob("History of $name for RK{$dimension}", $blob);
		}

		$sendto->reply($msg);
	}
}
