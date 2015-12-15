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
 *		command     = 'pocketboss',
 *		accessLevel = 'all',
 *		description = 'Shows what symbiants a pocketboss drops',
 *		help        = 'pocketboss.txt',
 *		alias       = 'pb'
 *	)
 *	@DefineCommand(
 *		command     = 'symbiant',
 *		accessLevel = 'all',
 *		description = 'Shows which pocketbosses drop a symbiant',
 *		help        = 'symbiant.txt',
 *		alias       = 'symb'
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
	public $util;
	
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
	 * @HandlesCommand("pocketboss")
	 * @Matches("/^pocketboss (.+)$/i")
	 */
	public function pocketbossCommand($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];
		$data = $this->pbSearchResults($search);
		$numrows = count($data);
		if ($numrows == 0) {
			$msg = "Could not find any pocket bosses that matched your search criteria.";
		} else if ($numrows == 1) {
			$name = $data[0]->pb;
			$blob .= $this->singlePbBlob($name);
			$msg = $this->text->make_blob("Remains of $name", $blob);
		} else {
			$blob = '';
			forEach ($data as $row) {
				$pbLink = $this->text->make_chatcmd($row->pb, "/tell <myname> pocketboss $row->pb");
				$blob .= $pbLink . "\n";
			}
			$msg = $this->text->make_blob("Search results for $search ($numrows)", $blob);
		}
		$sendto->reply($msg);
	}
	
	public function singlePbBlob($name) {
		$data = $this->db->query("SELECT * FROM pocketboss WHERE pb = ? ORDER BY ql", $name);
		$symbs = '';
		forEach ($data as $symb) {
			$name = "$symb->line $symb->slot Symbiant, $symb->type Unit Aban";
			$symbs .= $this->text->make_item($symb->itemid, $symb->itemid, $symb->ql, $name) . " ($symb->ql)\n";
		}
		
		$blob = "Location: <highlight>$symb->pb_location, $symb->bp_location<end>\n";
		$blob .= "Found on: <highlight>$symb->bp_mob, Level $symb->bp_lvl<end>\n\n";
		$blob .= $symbs;

		return $blob;
	}
	
	public function pbSearchResults($search) {
		$row = $this->db->queryRow("SELECT pb FROM pocketboss WHERE pb LIKE ? GROUP BY `pb` ORDER BY `pb`", $search);
		if ($row !== null) {
			return array($row);
		}
		
		$tmp = explode(" ", $search);
		list($query, $params) = $this->util->generateQueryFromParams($tmp, '`pb`');

		return $this->db->query("SELECT DISTINCT pb FROM pocketboss WHERE $query GROUP BY `pb` ORDER BY `pb`", $params);
	}
	
	/**
	 * @HandlesCommand("symbiant")
	 * @Matches("/^symbiant ([a-z]+)$/i")
	 * @Matches("/^symbiant ([a-z]+) ([a-z]+)$/i")
	 * @Matches("/^symbiant ([a-z]+) ([a-z]+) ([a-z]+)$/i")
	 */
	public function symbiantCommand($message, $channel, $sender, $sendto, $args) {
		$paramCount = count($args) - 1;

		$slot = '%';
		$symbtype = '%';
		$line = '%';
		
		$lines = $this->db->query("SELECT DISTINCT line FROM pocketboss");

		for ($i = 1; $i <= $paramCount; $i++) {
			switch (strtolower($args[$i])) {
				case "eye":
				case "ocular":
					$impDesignSlot = 'eye';
					$slot = "Ocular";
					break;
				case "brain":
				case "head":
					$impDesignSlot = 'head';
					$slot = "Brain";
					break;
				case "ear":
					$impDesignSlot = 'ear';
					$slot = "Ear";
					break;
				case "rarm":
					$impDesignSlot = 'rarm';
					$slot = "Right Arm";
					break;
				case "chest":
					$impDesignSlot = 'chest';
					$slot = "Chest";
					break;
				case "larm":
					$impDesignSlot = 'larm';
					$slot = "Left Arm";
					break;
				case "rwrist":
					$impDesignSlot = 'rwrist';
					$slot = "Right Wrist";
					break;
				case "waist":
					$impDesignSlot = 'waist';
					$slot = "Waist";
					break;
				case "lwrist":
					$impDesignSlot = 'lwrist';
					$slot = "Left Wrist";
					break;
				case "rhand":
					$impDesignSlot = 'rhand';
					$slot = "Right Hand";
					break;
				case "leg":
				case "legs":
				case "thigh":
					$impDesignSlot = 'legs';
					$slot = "Thigh";
					break;
				case "lhand":
					$impDesignSlot = 'lhand';
					$slot = "Left Hand";
					break;
				case "feet":
					$impDesignSlot = 'feet';
					$slot = "Feet";
					break;
				default:
					// check if it's a line
					forEach ($lines as $l) {
						if (strtolower($l->line) == strtolower($args[$i])) {
							$line = $l->line;
							break 2;
						}
					}

					// check if it's a type
					if (preg_match("/^art/i", $args[$i])) {
						$symbtype = "Artillery";
					} else if (preg_match("/^sup/i", $args[$i])) {
						$symbtype = "Support";
					} else if (preg_match("/^inf/i", $args[$i])) {
						$symbtype = "Infantry";
					} else if (preg_match("/^ext/i", $args[$i])) {
						$symbtype = "Extermination";
					} else if (preg_match("/^control/i", $args[$i])) {
						$symbtype = "Control";
					} else {
						return false;
					}
			}
		}

		$data = $this->db->query("SELECT * FROM pocketboss WHERE `slot` LIKE ? AND `type` LIKE ? AND `line` LIKE ? ORDER BY `ql` DESC, `type` ASC", $slot, $symbtype, $line);
		$numrows = count($data);
		if ($numrows != 0) {
			$implantDesignerLink = $this->text->make_chatcmd("implant designer", "/tell <myname> implantdesigner");
			$blob = "Click 'Add' to add symbiant to $implantDesignerLink.\n\n";
			forEach ($data as $row) {
				$name = "$row->line $row->slot Symbiant, $row->type Unit Aban";
				$impDesignerAddLink = $this->text->make_chatcmd("Add", "/tell <myname> implantdesigner $impDesignSlot symb $name");
				$blob .= "<pagebreak>" . $this->text->make_item($row->itemid, $row->itemid, $row->ql, $name)." ($row->ql) $impDesignerAddLink\n";
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
