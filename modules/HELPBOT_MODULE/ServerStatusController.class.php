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
			$servernum = $this->chatBot->vars['dimension'];
		} else {
			$servernum = $args[1];
		}
		
		// config file uses '4' to indicate test server
		if ($servernum == '4') {
			$servernum = 't';
		}
		
		if ($servernum != 1 && $servernum != 2 && $servernum != 't') {
			return false;
		}

		$server = $this->serverStatusManager->lookup($servernum);
		if ($server->errorCode != 0) {
			$msg = $server->errorInfo;
		} else {
			$link = '';

			if ($server->servermanager == 1) {
				$link .= "<highlight>Servermanager<end> is <green>UP<end>\n";
			} else {
				$link .= "<highlight>Servermanager<end> is <red>DOWN<end>\n";
			}

			if ($server->clientmanager == 1) {
				$link .= "<highlight>Clientmanager<end> is <green>UP<end>\n";
			} else {
				$link .= "<highlight>Clientmanager<end> is <red>DOWN<end>\n";
			}

			if ($server->chatserver == 1) {
				$link .= "<highlight>Chatserver<end> is <green>UP<end>\n\n";
			} else {
				$link .= "<highlight>Chatserver<end> is <red>DOWN<end>\n\n";
			}

			ksort($server->data);
			
			$vals = array_map(function($proz) {
				return str_replace('%', '', $proz["players"]);
			}, $server->data);
			
			$totalp = 0;
			forEach ($vals as $zone => $p) {
				$totalp += $p;
			}

			$y = $this->findLowestGreaterThanZero($vals);
			
			$sum = round($totalp / $y);
			do {
				$total = $sum;
				$y = $totalp / $total;  // percent per person
				$sum = 0;
				forEach ($vals as $zone => $p) {
					$num = round($p / $y);
					if ($p > 0 && $num == 0) {
						$num = 1;
					}
					$sum += $num;
				}
			} while ($total != $sum);
			
			$a = round($total / $totalp * 100);
			$link .= "Estimated total players online: <highlight>$a<end>\n\n";
			
			$link .= "Player distribution in % of total players online.\n";
			forEach ($vals as $zone => $p) {
				$num = round($p / $y);
				$link .= "$zone: <highlight>$num<end> ({$p}%)\n";
			}

			$msg = $this->text->make_blob("$server->name Server Status", $link);
		}

		$sendto->reply($msg);
	}
	
	public function findLowestGreaterThanZero($arr) {
		$val = 100;
		forEach ($arr as $p) {
			if ($p > 0 && $p < $val) {
				$val = $p;
			}
		}
		return $val;
	}
}
