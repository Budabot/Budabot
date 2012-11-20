<?php

/**
 * Authors: 
 *	- Imoutochan (RK1)
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'buffitem', 
 *		accessLevel = 'all', 
 *		description = 'Buffitem look up', 
 *		help        = 'buffitem.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'whatbuffs', 
 *		accessLevel = 'all', 
 *		description = 'Find items that buff', 
 *		help        = 'whatbuffs.txt'
 *	)
 */
class BuffItemsController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;

	/** @Inject */
	public $text;
	
	private $skill_list = array("Strength", "Stamina", "Agility", "Sense", "Psychic", "Intelligence", "Martial Arts", "Brawling", "Dimach", "Riposte", "Adventuring", "Swimming",
					"Body Dev", "Nano Pool", "1hb", "2hb", "1he", "2he", "Piercing", "Melee Energy", "Parry", "Sneak Attack", "Multi Melee", "Fast Attack",
					"Sharp Obj", "Grenade", "Heavy Weapons", "Bow", "Pistol", "Assault Rifle", "MG/SMG", "Shotgun", "Rifle", "Ranged Energy", "Fling Shot",
					"Aimed Shot", "Burst", "Full Auto", "Bow Special Attack", "Multi Ranged", "Mech Eng", "Pharma Tech", "Nano Prog", "Chemistry", "Psychology",
					"Elec Eng", "Quantum FT", "Weap Smith", "Comp Lit", "Tutoring", "Bio Met", "Mat Met", "Psy Mod", "Mat Crea", "Time Space", "Sens Imp",
					"First Aid", "Treatment", "Map Nav");
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'buffitems');
	}

	/**
	 * @HandlesCommand("buffitem")
	 * @Matches("/^buffitem (.+)$/i")
	 */
	public function buffitemCommand($message, $channel, $sender, $sendto, $args) {
		$name = $args[1];

		$matches = array();
		$found = 0;
		$dbparam = '%' . str_replace(" ", "%", $name) . '%';
		// search item line database
		$results = $this->db->query("SELECT * FROM buffitems WHERE item_name LIKE ? OR aliases LIKE ?", $dbparam, $dbparam);
		forEach ($results as $row) {
			$found++;
			$info =	$this->make_info($row);
			$matches []= array($row->item_name, $info);
		}

		if ($found == 0) {
			$msg = "Could not find any buff items that matched your search criteria.";
		} else {
			if ($found == 1) {
				$blob .= $matches[0][1];
				$blob .= "\n\nby Imoutochan, RK1";
				$msg = $this->text->make_blob("Buff Item - " . $matches[0][0], $blob);
			} else {
				$blob = "Buff item search results for <highlight>$name<end>:\n\n";
				forEach ($matches as $result) {
					$buffItemLink = $this->text->make_chatcmd($result[0], "/tell <myname> buffitem ".$result[0]);
					$blob .= "- $buffItemLink" . (count($result) == 3 ? " (".$result[2].")" : "")."\n";
				}
				$blob .= "\n\nby Imoutochan, RK1";
				$msg = $this->text->make_blob("Buff item search results (<highlight>$found<end>)", $blob);
			}
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs$/i")
	 */
	public function whatbuffsListCommand($message, $channel, $sender, $sendto, $args) {
		$blob = '';
		forEach ($this->skill_list as $skill) {
			$link = $this->text->make_chatcmd($skill, "/tell <myname> whatbuffs $skill");
			$blob .= $link . "\n";
		}
		$blob .= "\n\nby Imoutochan (RK1)";
		$msg = $this->text->make_blob("What Buffs Skills List", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("whatbuffs")
	 * @Matches("/^whatbuffs (.+)$/i")
	 */
	public function whatbuffsCommand($message, $channel, $sender, $sendto, $args) {
		$name = htmlspecialchars_decode(trim($args[1]), ENT_QUOTES);
		
		// check if key words are unambiguous
		$skills = array();
		forEach ($this->skill_list as $skill) {
			// hack so that skills which are part of another skill's name
			// are not hidden
			// eg. "bow" vs "bow special attack"
			if (strcasecmp($skill, $name) == 0) {
				$skills = array($skill);
				break;
			}
			if ($this->matches($skill, $name)) {
				$skills []= $skill;
			}
		}

		switch (count($skills)) {
			case 0:  // skill does not exist
				$msg = "Could not find a skill by that name.";
				break;

			case 1:  // exactly one matching skill
				$info = "";
				$found = 0;
				$skill = $skills[0];
				$dbparam = '%' . $skill . '%';
				$results = $this->db->query("SELECT * FROM buffitems WHERE boosts LIKE ? OR buff_break_points LIKE ?", $dbparam, $dbparam);
				forEach ($results as $row) {
					$found++;
					$info .= "- " . $this->text->make_chatcmd($row->item_name, "/tell <myname> buffitem $row->item_name") . "\n";
				}
				if ($found > 0) {
					$inside .= "Items that buff $skill:\n\n";
					$inside .= $info;
					$inside .= "\n\nby Imoutochan (RK1)";
					$msg = $this->text->make_blob("What Buffs '$skill' ($found)", $inside);
				} else {
					$msg = "Could not find any items that buff $skill.";
				}
				break;

			default:  // found more than 1 matching skill
				$info = "";
				forEach ($skills as $skill) {
					$info .= "- " . $this->text->make_chatcmd($skill, "/tell <myname> whatbuffs $skill") . "\n";
				}
				$blob = "Your query of <yellow>$name<end> matches more than one skill:\n\n";
				$blob .= $info;
				$blob .= "\n\nby Imoutochan (RK1)";
				$msg = $this->text->make_blob("What Buffs Skills (" . count($skills) . ")", $blob);
		}
		$sendto->reply($msg);
	}
	
	public function matches($probe, $comp) {
		$bits = explode(" ", $comp);
		$match = true;
		forEach ($bits as $substr) {
			if (stripos($probe, $substr) === false) {
				$match = false;
				break;
			}
		}
		return $match;
	}

	public function make_info($row) {
		$result = 
			"<header2>$row->item_name<end>:\n\n".
			"Category: <highlight>$row->category<end>\n".
			"Boosts: <highlight>$row->boosts<end>\n".
			"QL range: <highlight>$row->ql_range<end>\n".
			"Aquisition:\n<tab><highlight>$row->acquisition<end>\n".
			"Buff Break points:\n";
		
		forEach (explode("\\n", $row->buff_break_points) as $breakpoint) {
			$result .= "<tab><highlight>QL ".$breakpoint."<end>\n";
		}
		return $result;
	}
}
