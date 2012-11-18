<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'server', 
 *		accessLevel = 'all', 
 *		description = 'Show the server status', 
 *		help        = 'server.txt'
 *	)
 */
class ServerStatusController {

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
	public $serverStatusManager;

	/**
	 * @HandlesCommand("server")
	 * @Matches("/^server$/i")
	 * @Matches("/^server (.)$/i")
	 */
	public function playfieldListCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 1) {
			$dimension = $this->chatBot->vars['dimension'];
		} else {
			$dimension = $args[1];
		}
		
		// config file uses '4' to indicate test server
		if ($dimension == '4') {
			$dimension = 't';
		}
		
		if ($dimension != 1 && $dimension != 2 && $dimension != 't') {
			return false;
		}

		$server = $this->getServerInfo($dimension);
		if ($server === null) {
			$msg = "Could not get server status for RK$dimension.";
		} else {
			// sort by playfield name
			usort($server->data, function($playfield1, $playfield2) {
				return strcmp($playfield1->long_name, $playfield2->long_name);
			});
			
			$blob = '';

			if ($server->servermanager == 1) {
				$link .= "Servermanager is <green>UP<end>\n";
			} else {
				$link .= "Servermanager is <red>DOWN<end>\n";
			}

			if ($server->clientmanager == 1) {
				$link .= "Clientmanager is <green>UP<end>\n";
			} else {
				$link .= "Clientmanager is <red>DOWN<end>\n";
			}

			if ($server->chatserver == 1) {
				$link .= "Chatserver is <green>UP<end>\n\n";
			} else {
				$link .= "Chatserver is <red>DOWN<end>\n\n";
			}
			
			$link .= "Estimated total players online: <highlight>$server->totalPlayers<end>\n\n";
			
			$link .= "Player distribution in % of total players online.\n";
			forEach ($server->data as $playfield) {
				$link .= "$playfield->long_name: <highlight>$playfield->numPlayers<end> ({$playfield->percent}%)\n";
			}

			$msg = $this->text->make_blob("$server->name Server Status", $link);
		}

		$sendto->reply($msg);
	}
	
	public function findLowestGreaterThanZero($arr) {
		$val = 100;
		forEach ($arr as $playfield) {
			if ($playfield->percent > 0 && $playfield->percent < $val) {
				$val = $playfield->percent;
			}
		}
		return $val;
	}
	
	public function addNumPlayers($arr, $y) {
		forEach ($arr as $playfield) {
			$playfield->numPlayers = $num = round($playfield->percent / $y);
		}
	}
	
	public function getServerInfo($dimension) {
		$server = $this->serverStatusManager->lookup($dimension);

		$totalp = $this->getTotalPercent($server->data);

		$y = $this->findLowestGreaterThanZero($server->data);
		
		$sum = round($totalp / $y);
		do {
			$total = $sum;
			$y = $totalp / $total;  // percent per person
			$sum = 0;
			forEach ($server->data as $playfield) {
				$num = round($playfield->percent / $y);
				if ($playfield->percent > 0 && $num == 0) {
					$num = 1;
				}
				$sum += $num;
			}
		} while ($total != $sum);
		
		$server->totalPlayers = round($total / $totalp * 100);
		
		$this->addNumPlayers($server->data, $y);
		
		return $server;
	}
	
	public function getTotalPercent($arr) {
		$totalp = 0;
		forEach ($arr as $playfield) {
			$totalp += $playfield->percent;
		}
		return $totalp;
	}
}
