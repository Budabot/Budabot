<?php

namespace Budabot\User\Modules;

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
 *		description = 'Show which factions have gauntlet', 
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
		$response = $this->http->get("http://budabot.jkbff.com/gauntlet/index.php")->waitAndReturnResponse();

		if ($response->error) {
			$msg = "Could not get Gauntlet information: " . $response->error;
		} else if (empty($response->body)) {
			$msg = "Could not get Gauntlet information.";
		} else {
			$gauntlet = json_decode($response->body);
			
			$blob = $this->getBlob($gauntlet);
			$msg = $this->getMessage($gauntlet) . $this->text->make_blob("More info", $blob, "Gauntlet");
		}
		$sendto->reply($msg);
	}
	
	public function getMessage($gauntlet) {
		if ($gauntlet->Vizaresh != '-1') {
			$msg .= "<highlight>Vizaresh<end>: " . $this->util->unixtime_to_readable($gauntlet->Vizaresh, false) . ". ";
		} else {
			$msg .= "<highlight>Vizaresh<end>: Unknown spawn time. ";
		}
		
		if ($gauntlet->Clan != '-1') {
			$msg .= "<Clan>Clan<end>: " . $this->util->unixtime_to_readable($gauntlet->Clan, false) . ". ";
		}
		
		if ($gauntlet->Omni != '-1') {
			$msg .= "<Omni>Omni<end>: " . $this->util->unixtime_to_readable($gauntlet->Omni, false) . ". ";
		}
		return $msg;
	}
	
	public function getBlob($gauntlet) {
		$blob = '';
		if ($gauntlet->Clan != '-1') {
			$blob .= "<Clan>Clan<end> has the Gauntlet buff for " . $this->util->unixtime_to_readable($gauntlet->Clan) . ".\n";
		} else {
			$blob .= "<Clan>Clan<end> does not currently have the Gauntlet buff.\n";
		}
		if ($gauntlet->Omni != '-1') {
			$blob .= "<Omni>Omni<end> has the Gauntlet buff for " . $this->util->unixtime_to_readable($gauntlet->Omni) . ".\n";
		} else {
			$blob .= "<Omni>Omni<end> does not currently have the Gauntlet buff.\n";
		}
		if ($gauntlet->Vizaresh != '-1') {
			$blob .= "<highlight>Vizaresh<end> spawns in " . $this->util->unixtime_to_readable($gauntlet->Vizaresh) . ".\n";
		} else {
			$blob .= "<highlight>Vizaresh<end> unknown spawn time.\n";
		}
		$blob .= "\nGauntlet info provided by <highlight>Macross (RK2)<end>";
		return $blob;
	}
}
