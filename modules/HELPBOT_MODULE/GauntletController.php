<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'gauntlet', 
 *		accessLevel = 'member', 
 *		description = 'Show which faction(s) have gauntlet', 
 *		help        = 'gauntlet.txt'
 *	)
 */
class GauntletController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $http;
	
	/**
	 * @Setup
	 */
	public function setup() {
		
	}
	
	/**
	 * @HandlesCommand("gauntlet")
	 * @Matches("/^gauntlet$/i")
	 */
	public function gauntletCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->chatBot->vars['dimension'] != 2) {
			$msg = "This command is not availalble on this server.";
			$sendto->reply($msg);
			return;
		}
	
		$socket = fsockopen('70.117.151.241', '14523', $errno, $errstr, 5);
		$content = fgets($socket, 4096);

		if ($errno != 0) {
			$msg = "Coult not get Gauntlet information: " . $errstr;
		} else if ($content == 'none') {
			$msg = "No factions currently have Gauntlet buff.";
		} else {
			$statuses = explode('|', $content);
			$msg = '';
			$blob = '';
			forEach ($statuses as $status) {
				list($faction, $time) = explode(':', $status);
				$msg .= "$faction: <highlight>" . $this->util->unixtime_to_readable($time) . "<end>. ";
				$blob .= "$faction has the Gauntlet buff for <highlight>" . $this->util->unixtime_to_readable($time) . "<end>.\n";
			}
			
			$blob .= "\n<highlight>Gauntlet info provided by Macross (RK2)<end>";
			$msg .= $this->text->make_blob("More info", $blob, "Gauntlet");
		}
		$sendto->reply($msg);
	}
}
