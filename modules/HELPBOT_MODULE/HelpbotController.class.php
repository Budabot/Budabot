<?php

namespace Budabot\User\Modules;

use ParseError;

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
 *		command     = 'dyna', 
 *		accessLevel = 'all', 
 *		description = 'Search for RK Dynabosses', 
 *		help        = 'dyna.txt'
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
		
		$blob .= "Dyna camp information taken from CSP help files: http://creativestudent.com/ao/files-helpfiles.html";

		$msg = $this->text->makeBlob("Dynacamps ($count)", $blob);
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
		
		$blob .= "Dyna camp information taken from CSP help files: http://creativestudent.com/ao/files-helpfiles.html";

		$msg = $this->text->makeBlob("Dynacamps ($count)", $blob);
		$sendto->reply($msg);
	}
	
	private function formatResults($data) {
		$blob = '';
		forEach($data as $row) {
			$coordLink = $this->text->makeChatcmd("{$row->long_name} {$row->cX}x{$row->cY}", "/waypoint $row->cX $row->cY $row->playfield_id");
			$blob .="<pagebreak>$coordLink\n";
			$blob .="$row->mob - Level <highlight>{$row->minQl}-{$row->maxQl}<end>\n\n";
		}
		return $blob;
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

		$blob = "With a skill requirement of <highlight>$oe<end>, you will be\n".
			"Out of OE: <highlight>$lowoe100<end> or higher\n".
			"75%: <highlight>$lowoe75<end> - <highlight>" .($lowoe100 - 1). "<end>\n".
			"50%: <highlight>" .($lowoe50 + 1). "<end> - <highlight>" .($lowoe75 - 1). "<end>\n".
			"25%: <highlight>" .($lowoe25 + 1). "<end> - <highlight>$lowoe50<end>\n".
			"0%: <highlight>$lowoe25<end> or lower\n\n".
			"With a personal skill of <highlight>$oe<end>, you can use up to and be\n".
			"Out of OE: <highlight>$oe100<end> or lower\n".
			"75%: <highlight>" .($oe100 + 1). "<end> - <highlight>$oe75<end>\n".
			"50%: <highlight>" .($oe75 + 1). "<end> - <highlight>" .($oe50 - 1). "<end>\n".
			"25%: <highlight>$oe50<end> - <highlight>" .($oe25 - 1). "<end>\n".
			"0%: <highlight>$oe25<end> or higher\n\n".
			"WARNING: May be plus/minus 1 point!";

		$msg = "<highlight>{$lowoe100}<end> - {$oe} - <highlight>{$oe100}<end> " . $this->text->makeBlob('More info', $blob, 'Over-equipped Calculation');

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("calc")
	 * @Matches("/^calc (.+)$/i")
	 */
	public function calcCommand($message, $channel, $sender, $sendto, $args) {
		$calc = strtolower($args[1]);

		// check if the calc string includes not allowed chars
		$calc_check = strspn($calc, "0123456789.+-*%()/\\ ");

		// if no wrong char found
		if ($calc_check == strlen($calc)) {
			$result = "";
			// do the calculations
			try {
				$calc = "\$result = ".$calc.";";
				eval($calc);
				
				$result = round($result, 4);
				$msg = $args[1]." = <highlight>".$result."<end>";
				$sendto->reply($msg);
			} catch (ParseError $e) {
				return false;
			}
		} else {
			return false;
		}
	}
}
