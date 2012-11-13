<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'pb', 
 *		accessLevel = 'all', 
 *		description = 'Shows what symbiants a pocketboss drops', 
 *		help        = 'pb.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'symb', 
 *		accessLevel = 'all', 
 *		description = 'Shows which pocketbosses drop a symbiant',
 *		help        = 'symb.txt'
 *	)
 */
class PocketbossController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $db;
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "pocketboss");
	}
	
	/**
	 * @HandlesCommand("pb")
	 * @Matches("/^pb (.+)$/i")
	 */
	public function pbCommand($message, $channel, $sender, $sendto, $args) {
		$search = str_replace(" ", "%", $args[1]);
		$data = $this->db->query("SELECT * FROM pbdb WHERE `pb` LIKE ? GROUP BY `pb` ORDER BY `pb`", '%' . $search . '%');
		$numrows = count($data);
		if ($numrows >= 1 && $numrows <= 5) {
			$msg = "Pocketbosses matching: ";
			forEach ($data as $row) {
				$blob = "Location: <highlight>$row->pb_location<end>\n";
				$blob .= "Found on: <highlight>$row->bp_mob<end>\n";
				$blob .= "Mob Level: <highlight>$row->bp_lvl<end>\n";
				$blob .= "General Location: <highlight>$row->bp_location<end>\n";
				$blob .= "_____________________________\n";
				$data2 = $this->db->query("SELECT * FROM pbdb WHERE pb = ? ORDER BY ql", $row->pb);
				forEach ($data2 as $symb) {
					$name = "QL $symb->ql $symb->line $symb->slot Symbiant, $symb->type Unit Aban";
					$blob .= $this->text->make_item($symb->itemid, $symb->itemid, $symb->ql, $name)."\n";
				}
				$msg .= "\n" . $this->text->make_blob("Remains of $row->pb", $blob);
			}
		} else if ($numrows > 5) {
			$msg = "Too many results.";
		} else {
			$msg = "Could not find any Pocketbosses matching your search criteria.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("symb")
	 * @Matches("/^symb ([a-z]+)$/i")
	 * @Matches("/^symb ([a-z]+) ([a-z]+)$/i")
	 */
	public function symbCommand($message, $channel, $sender, $sendto, $args) {
		$paramCount = count($args) - 1;

		$slot = '%';
		$symbtype = '%';

		for ($i = 1; $i <= $paramCount; $i++) {
			switch (strtolower($args[$i])) {
				case "eye":
				case "ocular":
					$slot = "Ocular";
					break;
				case "brain":
				case "head":
					$slot = "Brain";
					break;
				case "ear":
					$slot = "Ear";
					break;
				case "rarm":
					$slot = "Right Arm";
					break;
				case "chest":
					$slot = "Chest";
					break;
				case "larm":
					$slot = "Left Arm";
					break;
				case "rwrist":
					$slot = "Right Wrist";
					break;
				case "waist":
					$slot = "Waist";
					break;
				case "lwrist":
					$slot = "Left Wrist";
					break;
				case "rhand":
					$slot = "Right Hand";
					break;
				case "leg":
				case "legs":
				case "thigh":
					$slot = "Thigh";
					break;
				case "lhand":
					$slot = "Left Hand";
					break;
				case "feet":
					$slot = "Feet";
					break;
				default:
					if (preg_match("/^a/i", $args[$i])) {
						$symbtype = "Artillery";
					} else if (preg_match("/^s/i", $args[$i])) {
						$symbtype = "Support";
					} else if (preg_match("/^i/i", $args[$i])) {
						$symbtype = "Infantry";
					} else if (preg_match("/^e/i", $args[$i])) {
						$symbtype = "Extermination";
					} else if (preg_match("/^c/i", $args[$i])) {
						$symbtype = "Control";
					}
			}
		}

		$data = $this->db->query("SELECT * FROM pbdb WHERE `slot` LIKE ? AND `type` LIKE ? ORDER BY `ql` DESC, `type` ASC", $slot, $symbtype);
		$numrows = count($data);
		if ($numrows != 0) {
			$blob = '';
			forEach ($data as $row) {
				$name = "QL $row->ql $row->line $row->slot Symbiant, $row->type Unit Aban";
				$blob .= "<pagebreak>" . $this->text->make_item($row->itemid, $row->itemid, $row->ql, $name)."\n";
				$blob .= "Found on " . $this->text->make_chatcmd($row->pb, "/tell <myname> pb $row->pb");
				$blob .= "\n\n";
			}
			$msg = $this->text->make_blob("Symbiant Search Results ($numrows)", $blob);
		} else {
			$msg = "Could not find any symbiants that matched your search criteria.";
		}

		$sendto->reply($msg);
	}
}
