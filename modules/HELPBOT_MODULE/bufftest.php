<?php
if (!function_exists("get_skill")) {
	function get_skill($input) {
		$input = strtolower($input);
		switch ($input) {
			case 'agi':
			case 'agil':
			case 'agility':
				$skill = "Agility";
				break;
			case 'int':
			case 'intel':
			case 'intell':
			case 'intelligence':
				$skill = "Intelligence";
				break;
			case 'psy':
			case 'psyc':
			case 'psych':
			case 'psychic':
				$skill = "Psychic";
				break;
			case 'sen':
			case 'sens':
			case 'sense':
				$skill = "Sense";
				break;
			case 'sta':
			case 'stam':
			case 'stami':
			case 'stamina':
				$skill = "Stamina";
				break;
			case 'str':
			case 'stren':
			case 'strength':
				$skill = "Strength";
				break;
			case 'treat':
			case 'treatment':
				$skill = "Treatment";
				break;
			//Add new cases for each new skill
			default:
				$skill = '';
		}
		
		return $skill;
	}
}	
// Makes main selection screen
if (preg_match("/^bufftest$/i", $message, $arr)) {
	$blob = "<highlight>Ability Listings:<end>\n";
	$blob .= Text::make_chatcmd("Agility", "/tell <myname> bufftest Agility") . " - ";
	$blob .= Text::make_chatcmd("Intelligence", "/tell <myname> bufftest Intelligence") . " - ";
	$blob .= Text::make_chatcmd("Psychic", "/tell <myname> bufftest Psychic") . " - ";
	$blob .= Text::make_chatcmd("Sense", "/tell <myname> bufftest Sense") . " - ";
	$blob .= Text::make_chatcmd("Stamina", "/tell <myname> bufftest Stamina") . " - ";
	$blob .= Text::make_chatcmd("Strength", "/tell <myname> bufftest Strength");
	// Add additional skills over time
	$blob .= "\n\n<highlight>Skill Listings:<end>\n";
	$blob .= Text::make_chatcmd("Treatment", "/tell <myname> bufftest Treatment");

	$msg = Text::make_blob("Buff item skill selection", $blob);
	$chatBot->send($msg, $sendto);
// Creates the list of items
} else if (preg_match("/^bufftest (.+)$/i", $message, $arr)) {
	$name = $arr[1];
	$skill = get_skill($name);

	// Allows only valid searches. Must add additional skills in the get_skill function to bypass check
	if (!$skill){
		$syntax_error = true;
		return;
	}
	$data = $db->query("SELECT * FROM buffitems WHERE skill = ? ORDER BY type, buffed DESC", $skill);
	
	if (count($data) > 0) {
		$blob = '';
		$currentType == '';
		forEach ($data as $row) {
			if ($row->type != $currentType) {
				if ($row->type == 'weap') {
					$blob .= "\n<highlight>Weapon Buff List<end>\n\n";
				} else if ($row->type == "armor") {
					$blob .= "\n<highlight>Armor Buff List<end>\n\n";
				} else if ($row->type == "util") {
					$blob .= "\n<highlight>Utility Buff List<end>\n\n";
				}
				$currentType = $row->type;
			}
			
			$blob .= Text::make_item($row->lowid, $row->highid, $row->minql, $row->name) . " Buff Amount: <highlight>$row->buffed<end> - <highlight>$row->info<end>\n";
		}
		$msg = Text::make_blob("Buff Item list ($skill)", $blob);
	} else {
		$msg = "No items that buff <highlight>$skill<end> could be found.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
	
?>