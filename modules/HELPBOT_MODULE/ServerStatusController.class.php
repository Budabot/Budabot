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
	public $db;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $http;
	
	/** @Inject */
	public $util;
	
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
				$val = $playfield2->numPlayers - $playfield1->numPlayers;
				if ($val == 0) {
					return strcmp($playfield1->long_name, $playfield2->long_name);
				} else {
					return $val;
				}
			});
			
			$blob = '';
			$blob .= "Estimated total players online: <highlight>$server->totalPlayers<end>\n\n";
			$blob .= "Note that these numbers only include players that are logged in with the game client. These numbers do not include bots or players logged in with AORC, The Leet, etc.\n\n";
			
			forEach ($server->data as $playfield) {
				$blob .= "$playfield->long_name: <highlight>$playfield->numPlayers<end> ({$playfield->percent}%)\n";
			}

			$msg = $this->text->make_blob("$server->name Server Status ($server->totalPlayers)", $blob);
		}

		$sendto->reply($msg);
	}
	
	public function addNumPlayers($arr, $y) {
		forEach ($arr as $playfield) {
			$playfield->numPlayers = $num = round($playfield->percent / $y);
		}
	}
	
	public function getServerInfo($dimension) {
		$server = $this->lookup($dimension);

		$list = array_filter(
			array_unique(
				array_map(function ($playfield) {
					return $playfield->percent;
				}, $server->data)
			), function ($per) {
				return $per == 0.0 ? false : true;
			}
		);
		sort($list);
		
		$roundingVariation = 0.05;
		$per = array_shift($list);
		$y = $this->calcPercentPerPlayer($per - $roundingVariation, $per + $roundingVariation, 2, $list);
		
		$server->totalPlayers = round(100 / $y);
		
		$this->addNumPlayers($server->data, $y);
		
		return $server;
	}
	
	public function calcPercentPerPlayer($min, $max, $num, $list) {
		if (empty($list)) {
			return ($min + $max) / 2;
		}
		
		$roundingVariation = 0.05;
		$base = $list[0];
		$currentMin = ($base - $roundingVariation) / $num;
		$currentMax = ($base + $roundingVariation) / $num;
		$newMin = max($currentMin, $min);
		$newMax = min($currentMax, $max);
		
		if ($base > round($num * $max, 1)) {
			return $this->calcPercentPerPlayer($min, $max, $num + 1, $list);
		} else if ($base < round($num * $min, 1)) {
			return $this->calcPercentPerPlayer($min / $num * ($num - 1), $max / $num * ($num - 1), $num - 1, $list);
		} else {
			array_shift($list);
			return $this->calcPercentPerPlayer($newMin, $newMax, $num + 1, $list);
		}
	}
	
	public function lookup($rk_num) {
		$response = $this->http->get("http://probes.funcom.com/ao.xml")->waitAndReturnResponse();
		
		if ($response->error) {
			return null;
		}

		$data = xml::spliceData($response->body, "<dimension name=\"d$rk_num", "</dimension>");
		if (!$data) {
			return null;
		}
		
		$obj = new ServerStatus();

		preg_match("/locked=\"(0|1)\"/i", $data, $tmp);
		$obj->locked = $tmp[1];

		preg_match("/<omni percent=\"([0-9.]+)\"\/>/i", $data, $tmp);
		$obj->omni = $tmp[1];
		preg_match("/<neutral percent=\"([0-9.]+)\"\/>/i", $data, $tmp);
		$obj->neutral = $tmp[1];
		preg_match("/<clan percent=\"([0-9.]+)\"\/>/i", $data, $tmp);
		$obj->clan = $tmp[1];

		preg_match("/<servermanager status=\"([0-9]+)\"\/>/i", $data, $tmp);
		$obj->servermanager = $tmp[1];
		preg_match("/<clientmanager status=\"([0-9]+)\"\/>/i", $data, $tmp);
		$obj->clientmanager = $tmp[1];
		preg_match("/<chatserver status=\"([0-9]+)\"\/>/i", $data, $tmp);
		$obj->chatserver = $tmp[1];

		preg_match("/display-name=\"(.+)\" loadmax/i", $data, $tmp);
		$obj->name = $tmp[1];

		$data = xml::spliceMultiData($data, "<playfield", "/>");
		forEach ($data as $hdata) {
			if (preg_match("/id=\"(.+)\" name=\"(.+)\" status=\"(.+)\" load=\"(.+)\" players=\"(.+)%\"/i", $hdata, $arr)) {
				$playfield = new stdClass;
				$playfield->id = $arr[1];
				$playfield->long_name = $arr[2];
				$playfield->status = $arr[3];
				$playfield->load = $arr[4];
				$playfield->percent = $arr[5];
				$obj->data[$arr[1]] = $playfield;
			}
		}

		return $obj;
	}
}

class ServerStatus {
	public $data;
	public $servermanager;
	public $clientmanager;
	public $chatserver;
	public $locked;
	public $omni;
	public $neutral;
	public $clan;
	public $name;
}
