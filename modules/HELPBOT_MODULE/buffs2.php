<?php

// Makes main selection screen
if (preg_match("/^bufftest$/i", $message, $arr)) {
	$blob = "<header> :::::: Buff item skill selection :::::: <end>\n\n";
	$blob .= "<highlight>Ability Listings:<end>\n";
	$blob .= Text::make_chatcmd("Agility", "/tell <myname> buffitems Agility") . " - ";
	$blob .= Text::make_chatcmd("Intelligence", "/tell <myname> buffitems Intelligence") . " - ";
	$blob .= Text::make_chatcmd("Psychic", "/tell <myname> buffitems Psychic") . " - ";
	$blob .= Text::make_chatcmd("Sense", "/tell <myname> buffitems Sense") . " - ";
	$blob .= Text::make_chatcmd("Stamina", "/tell <myname> buffitems Stamina") . " - ";
	$blob .= Text::make_chatcmd("Strength", "/tell <myname> buffitems Strength");
	// Add additional skills over time
	$blob .= "\n\n<highlight>Skill Listings:<end>\n";
	$blob .= Text::make_chatcmd("Treatment", "/tell <myname> buffitems Treatment");

	$msg = Text::make_blob("test", $blob);
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
	$blob .=  "<header>:::::: $ability Buffing Items ::::::<end>\n";
	$weap = 1;
	$armor = 1;
	$util = 1;
	forEach ($data as $row) {
		//print_r($row);
		if ($row->type == "weap") {
			if ($weap == 1) {
				$blob .= "\n<highlight>Weapon Buff List<end>\n\n";
				$weap = 0;
			}
			$blob .= Text::make_item($row->lowid, $row->highid, $row->minql, $row->name) . " Buff Amount: <highlight>$row->buffed<end> - $row->info\n";
		}
		if ($row->type == "armor") {
			if ($armor == 1) {
				$blob .= "\n<highlight>Armor Buff List<end>\n\n";
				$armor = 0;
			}
			$blob .= Text::make_item($row->lowid, $row->highid, $row->minql, $row->name) . " Buff Amount: <highlight>$row->buffed<end> - $row->info\n";
		}
		if ($row->type == "util") {
			if ($util == 1) {
				$blob .= "\n<highlight>Utility Buff List<end>\n\n";
				$util = 0;
			}
			$blob .= Text::make_item($row->lowid, $row->highid, $row->minql, $row->name) . " Buff Amount: <highlight>$row->buffed<end> - $row->info\n";
		}
	}
	$msg = Text::make_blob("Buff Item list ($ability)", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
	
?>