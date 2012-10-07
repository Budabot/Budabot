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
		if ($history->errorCode != 0) {
			$msg = $history->errorInfo;
		} else {
			$blob = "Date           Level    AI     Faction      Guild(rank) \n";
			$blob .= "________________________________________________ \n";
			forEach ($history->data as $key => $data) {
				$level = $data["level"];

				if ($data["ailevel"] == "") {
					$ailevel = "<green>0<end>";
				} else {
					$ailevel = "<green>".$data["ailevel"]."<end>";
				}

				if ($data["faction"] == "Omni") {
					$faction = "<omni>Omni<end>";
				} else if ($data["faction"] == "Clan") {
					$faction = "<clan>Clan<end>";
				} else {
					$faction = "<neutral>Neutral<end>";
				}

				if ($data["guild"] == "") {
					$guild = "Not in a guild";
				} else {
					$guild = $data["guild"]."(".$data["rank"].")";
				}

				$blob .= "$key |  $level  | $ailevel | $faction | $guild\n";
			}
			$msg = $this->text->make_blob("History of $name for RK{$dimension}", $blob);
		}

		$sendto->reply($msg);
	}
}
