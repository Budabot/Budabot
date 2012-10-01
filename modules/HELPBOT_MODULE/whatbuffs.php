<?php

$skill_list = array("Strength", "Stamina", "Agility", "Sense", "Psychic", "Intelligence", "Martial Arts", "Brawling", "Dimach", "Riposte", "Adventuring", "Swimming",
					"Body Dev", "Nano Pool", "1hb", "2hb", "1he", "2he", "piercing", "melee energy", "parry", "sneak attack", "multi melee", "fast attack",
					"Sharp Obj", "Grenade", "Heavy Weapons", "Bow", "Pistol", "Assault Rif", "MG/SMG", "Shotgun", "Rifle", "Ranged Energy", "Fling Shot",
					"Aimed Shot", "Burst", "Full Auto", "Bow Special Attack", "Multi Ranged", "Mech Eng", "Pharma Tech", "Nano Prog", "Chemistry", "Psychology",
					"Elec Eng", "Quantum FT", "Weap Smith", "Comp Lit", "Tutoring", "Bio Met", "Mat Met", "Psy Mod", "Mat Crea", "Time Space", "Sens Imp",
					"First Aid", "Treatment", "Map Nav");

if (preg_match("/^whatbuffs (.+)$/i", $message, $arr)) {
	$name = trim($arr[1]);
	// check if key words are unambiguous
	$skills = array();
	$results = array();
	forEach ($skill_list as $skill) {
		if (matches($skill, $name)) {
			array_unshift($skills, $skill);
		}
	}

	switch (sizeof($skills)) {
		case 0:  // skill does not exist
			$sendto->reply("Could not find a skill by that name.");
			return;

		case 1:  // exactly one matching skill
			$info = "";
			$found = 0;
			$dbparam = '%' . $skills[0] . '%';
			$results = $db->query("SELECT * FROM buffitems WHERE boosts LIKE ? OR buff_break_points LIKE ?", $dbparam, $dbparam);
			forEach ($results as $row) {
				$found++;
				$info .= "- " . Text::make_chatcmd($row->item_name, "/tell <myname> buffitem $row->item_name") . "\n";
			}
			if ($found > 0) {								// found items that modify this skill
				$inside = "Your query of <yellow>$name<end> yielded the following results:\n\n";
				$inside .= "Items that buff ".$skills[0].":\n\n";
				$inside .= $info;
				$inside .= "\n\nby Imoutochan, RK1";
				$windowlink = Text::make_blob("What Buffs '$name' ($found)", $inside);
				$sendto->reply($windowlink);
				return;
			} else {
				$sendto->reply("Nothing that buffs ".$skills[0]." in my database.");
				return;
			}
			break;

		default:  // found more than 1 matching skill
			$info = "";
			forEach ($skills as $skill) {
				$info .= "- " . Text::make_chatcmd($skill, "/tell <myname> whatbuffs $skill") . "\n";
			}
			$inside = "Your query of <yellow>$name<end> matches more than one skill:\n\n";
			$inside .= $info;
			$inside .= "\n\nby Imoutochan, RK1";
			$windowlink = Text::make_blob("What Buffs Skills (" . count($skills) . ")", $inside);
			$sendto->reply($windowlink);
			return;
	}
} else {
	$syntax_error = true;
}

?>
