<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *	- Neksus (RK2)
 *	- Mdkdoc420 (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'mobloot', 
 *		accessLevel = 'all', 
 *		description = 'Show loot QL info', 
 *		help        = 'mobloot.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'dyna', 
 *		accessLevel = 'all', 
 *		description = 'Search for RK Dynabosses', 
 *		help        = 'dyna.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'inspect', 
 *		accessLevel = 'all', 
 *		description = 'Inspect Christmas/Eart Gifts and Peren. Containers', 
 *		help        = 'inspect.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'oe', 
 *		accessLevel = 'all', 
 *		description = 'Over-equipped calculation', 
 *		help        = 'oe.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'calc', 
 *		accessLevel = 'all', 
 *		description = 'Calculator', 
 *		help        = 'calculator.txt'
 *	)
 */
class HelpbotController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'dyna');
	}

	/**
	 * @HandlesCommand("mobloot")
	 * @Matches("/^mobloot ([0-9]+)$/i")
	 */
	public function moblootCommand($message, $channel, $sender, $sendto, $args) {
		$lvl = trim($args[1]);

		if ($lvl > 300 || $lvl < 1) {
			$msg = "Level entered is out of range... please enter a number between <highlight>1 and 300<end>.";
		} else {
			$high = floor($lvl * 1.25);
			$low = ceil($lvl * 0.75);

			$msg .= "Monster level <highlight>". $lvl ."<end>: ";
			$msg .= "QL <highlight>".$low."<end> - <highlight>".$high."<end>";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("dyna")
	 * @Matches("/^dyna ([0-9]+)$/i")
	 */
	public function dynaLevelCommand($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];
		$range1 = $search - 25;
		$range2 = $search + 25;
		$data = $this->db->query("SELECT * FROM dynadb d JOIN playfields p ON d.playfield_id = p.id WHERE minQl > ? AND minQl < ? ORDER BY `minQl`", $range1, $range2);
		$count = count($data);

		$blob = "Results of Dynacamp Search for '$search'\n\n";

		$blob .= $this->formatResults($data);

		$msg = $this->text->make_blob("Dynacamps ($count)", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("dyna")
	 * @Matches("/^dyna (.+)$/i")
	 */
	public function dynaNameCommand($message, $channel, $sender, $sendto, $args) {
		$search = str_replace(" ", "%", $args[1]);
		$data = $this->db->query("SELECT * FROM dynadb d JOIN playfields p ON d.playfield_id = p.id WHERE long_name LIKE ? OR short_name LIKE ? OR mob LIKE ? ORDER BY `minQl`", "%{$search}%", "%{$search}%", "%{$search}%");
		$count = count($data);

		$blob = "Results of Dynacamp Search for '$search'\n\n";

		$blob .= $this->formatResults($data);

		$msg = $this->text->make_blob("Dynacamps ($count)", $blob);
		$sendto->reply($msg);
	}
	
	private function formatResults($data) {
		$blob = '';
		forEach($data as $row) {
			$coordLink = $this->text->make_chatcmd("{$row->cX}x{$row->cY} {$row->long_name}", "/waypoint $row->cX $row->cY $row->playfield_id");
			$blob .="<pagebreak><highlight>$row->long_name<end>:  Co-ordinates $coordLink\n";
			$blob .="Mob Type:  $row->mob\n";
			$blob .="Level: <highlight>{$row->minQl}-{$row->maxQl}<end>\n\n";
		}
		return $blob;
	}
	
	/**
	 * Can identify Christmas Gift, Expensive Gift from Earth, and Light Perennium Container
	 *
	 * @HandlesCommand("inspect")
	 * @Matches("/^inspect (.+)$/i")
	 */
	public function inspectCommand($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];
		if (preg_match("~<a href=\"itemref://(\\d{6})/(\\d{6})/(\\d{1,3})\">([^<]+)</a>~i", $search, $matches)) {
			$highId = $matches[2];
			$ql = $matches[3];

			switch ($highId) {
				case 205842:
					$type = "Funny Arrow";
					break;
				case 205843:
					$type = "Monster Sunglasses";
					break;
				case 205844:
					$type = "Karlsson Propellor Cap";
					break;
				case 216286:
					$type = "Funk Flamingo Sunglasses or Disco Duck Sunglasses or Electric Boogie Sunglasses or Gurgling River Sprite";
					break;
				case 245658:
					$type = "Blackpack";
					break;
				case 245596:
					$type = "Doctor's Pill Pack";
					break;
				case 245594:
					$type = "Syndicate Shades";
					break;
				default:
					$type = "Unidentified";
			}
			$msg = "QL $ql of $type";
			$sendto->reply($msg);
		} else {
			return false;
		}
	}
	
	/**
	 * @HandlesCommand("oe")
	 * @Matches("/^oe ([0-9]+)$/i")
	 */
	public function oeCommand($message, $channel, $sender, $sendto, $args) {
		$oe = $args[1];
		$oe100 = (int)floor($oe / 0.8);
		$lowoe100 = (int)floor($oe * 0.8);
		$oe75 = (int)floor($oe / 0.6);
		$lowoe75 = (int)floor($oe * 0.6);
		$oe50 = (int)floor($oe / 0.4);
		$lowoe50 = (int)floor($oe * 0.4);
		$oe25 = (int)floor($oe / 0.2);
		$lowoe25 = (int)floor($oe * 0.2);

		$blob = "With a weapons skill requirement of <highlight>$oe<end>, you will OE at:\n".
			"Out of OE: <highlight>$lowoe100<end> or higher\n".
			"75%: <highlight>$lowoe75<end> - <highlight>" .($lowoe100 - 1). "<end>\n".
			"50%: <highlight>" .($lowoe50 + 1). "<end> - <highlight>" .($lowoe75 - 1). "<end>\n".
			"25%: <highlight>" .($lowoe25 + 1). "<end> - <highlight>$lowoe50<end>\n".
			"0%: <highlight>$lowoe25<end> or lower\n\n".
			"With a personal skill of <highlight>$oe<end>, you can use up to and be:\n".
			"Out of OE: <highlight>$oe100<end> or lower\n".
			"75%: <highlight>" .($oe100 + 1). "<end> - <highlight>$oe75<end>\n".
			"50%: <highlight>" .($oe75 + 1). "<end> - <highlight>" .($oe50 - 1). "<end>\n".
			"25%: <highlight>$oe50<end> - <highlight>" .($oe25 - 1). "<end>\n".
			"0%: <highlight>$oe25<end> or higher\n\n".
			"WARNING: May be plus/minus 1 point!";

		$msg = "<orange>{$lowoe100}<end> - <yellow>{$oe}<end> - <orange>{$oe100}<end> " . $this->text->make_blob('More info', $blob, 'Over-equipped Calculation');

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("calc")
	 * @Matches("/^calc (.+)$/i")
	 */
	public function calcCommand($message, $channel, $sender, $sendto, $args) {
		$calc = strtolower($args[1]);

		// check if the calc string includes not allowed chars
		$calc_check = strspn($calc, "0123456789.,+-*x%()/\\ ");

		// if no wrong char found
		if ($calc_check == strlen($calc)) {
			$result = "";
			// do the calculations
			$calc = "\$result = ".$calc.";";
			eval($calc);
			// if calculation is succesful
			if (is_numeric($result)) {
				$result = round($result, 4);
				$msg = $args[1]." = <highlight>".$result."<end>";
				$sendto->reply($msg);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
