<?php

namespace Budabot\User\Modules;

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
		$rk_num = $this->chatBot->vars['dimension'];
		if (count($args) == 3) {
			$rk_num = $args[2];
		}

		$history = $this->playerHistoryManager->lookup($name, $rk_num);
		if ($history === null) {
			$msg = "Could not get History of $name on RK$rk_num.";
		} else {
			$blob = "Date           Level    AI     Faction    Breed        Guild (rank)\n";
			$blob .= "________________________________________________ \n";
			forEach ($history->data as $entry) {
				$date = date("d-M-Y", $entry->last_changed);

				if ($entry->deleted == 1) {
					$blob .= "$date |   <red>DELETED<end>\n";
				} else {
					if ($entry->defender_rank == "") {
						$ailevel = "<green>0<end>";
					} else {
						$ailevel = "<green>$entry->defender_rank<end>";
					}

					if ($entry->faction == "Omni") {
						$faction = "<omni>Omni<end>";
					} else if ($entry->faction == "Clan") {
						$faction = "<clan>Clan<end>";
					} else {
						$faction = "<neutral>Neutral<end>";
					}

					if ($entry->guild_name == "") {
						$guild = "Not in a guild";
					} else {
						$guild = $entry->guild_name . " (" . $entry->guild_rank_name . ")";
					}

					$blob .= "$date |  $entry->level  | $ailevel | $faction | $entry->breed | $guild\n";
				}
			}
			$blob .= "\nHistory provided by Budabot.com and Auno.org";
			$msg = $this->text->makeBlob("History of $name for RK{$rk_num}", $blob);
		}

		$sendto->reply($msg);
	}
}
