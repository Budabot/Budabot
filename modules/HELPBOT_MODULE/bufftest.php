<?php

// Makes main selection screen
if (preg_match("/^bufftest$/i", $message, $arr)) {
	$blob = "<header> :::::: Buff item skill selection :::::: <end>\n\n";
	$blob .= "<highlight>Ability Listings:<end>\n";
	$blob .= Text::make_chatcmd("Agility", "/tell <myname> bufftest Agility") . " - ";
	$blob .= Text::make_chatcmd("Intelligence", "/tell <myname> bufftest Intelligence") . " - ";
	$blob .= Text::make_chatcmd("Psychic", "/tell <myname> bufftest Psychic") . " - ";
	$blob .= Text::make_chatcmd("Sense", "/tell <myname> bufftest Sense") . " - ";
	$blob .= Text::make_chatcmd("Stamina", "/tell <myname> bufftest Stamina") . " - ";
	$blob .= Text::make_chatcmd("Strength", "/tell <myname> bufftest Strength");
	// Add additional skills over time
	$blob .= "\n\n<highlight>Skill Listings:<end>\n";
	$blob .= Text::make_chatcmd("Treatment", "/tell <myname> bufftest Treatment");

	$msg = Text::make_blob("Buff Items", $blob);
	$chatBot->send($msg, $sendto);
// Creates the list of items
} else if (preg_match("/^bufftest (.+)$/i", $message, $arr)) {
	$name = $arr[1];
	$ability = Util::get_ability($arr[1], true);

	// Allows only valid searches. Must add additional skills in the get_ability function to bypass check
	if (!$ability){
		$syntax_error = true;
		return;
	}
	$data = $db->query("SELECT * FROM buffitems WHERE skill = ? ORDER BY type, buffed DESC", $ability);
	
	if (count($data) > 0) {
		$blob .=  "<header>:::::: $ability Buffing Items ::::::<end>\n";
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
		$msg = Text::make_blob("Buff Item list ($ability)", $blob);
	} else {
		$msg = "No items that buff <highlight>$name<end> could be found.";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
	
?>