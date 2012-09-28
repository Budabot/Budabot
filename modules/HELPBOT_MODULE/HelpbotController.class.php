<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
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
	public $chatBot;

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

		$blob = "Results Of Dynacamp Search For '$search'\n\n";

		$blob .= $this->formatResults($data);

		$msg = $this->text->make_blob("Dynacamps ($count)", $blob);
		$sendto->reply($msg);
	}
	
	private function formatResults($data) {
		$blob = '';
		forEach($data as $row) {
			$coordLink = $this->text->make_chatcmd("{$row->cX}x{$row->cY} {$row->long_name}", "/waypoint $row->cX $row->cY $row->playfield_id");
			$blob .="<pagebreak>$row->long_name:  Co-ordinates $coordLink\n";
			$blob .="Mob Type:  $row->mob\n";
			$blob .="Level: {$row->minQl}-{$row->maxQl}\n\n";
		}
		return $blob;
	}
}
