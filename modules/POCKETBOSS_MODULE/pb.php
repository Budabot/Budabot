<?php

if (preg_match("/^pb (.+)$/i", $message, $arr)) {
	$search = str_replace(" ", "%", $arr[1]);
	$search = str_replace("'", "''", $search);
  	$db->query("SELECT * FROM pbdb WHERE `pb` LIKE '%{$search}%' GROUP BY `pb` ORDER BY `pb`");
	$numrows = $db->numrows();
  	if ($numrows >= 1 && $numrows <= 5) {
		$msg = "Pocketbosses matching: ";
  	  	$data = $db->fObject("all");
		forEach ($data as $row) {
			$blob  = "<header>:::::: Remains of $row->pb :::::<end>\n\n";
			$blob .= "<highlight>Location:<end> $row->pb_location\n";
			$blob .= "<highlight>Found on:<end> $row->bp_mob\n";
			$blob .= "<highlight>Mob Level:<end> $row->bp_lvl\n";
			$blob .= "<highlight>General Location:<end> $row->bp_location\n";
			$blob .= "_____________________________\n";
			$db->query("SELECT * FROM pbdb WHERE pb = '$row->pb' ORDER BY ql");
			$data2 = $db->fObject('all');
			forEach ($data2 as $symb) {
			  	$name = "QL $symb->ql $symb->line $symb->slot Symbiant, $symb->type Unit Aban";
			  	$blob .= Text::make_item($symb->itemid, $symb->itemid, $symb->ql, $name)."\n";
			}
			$msg .= "\n".Text::make_blob("Remains of $row->pb", $blob);
		}
	} else if ($numrows > 5) {
		$msg = "Too many results.";
	} else {
		$msg = "Could not find any Pocketbosses matching your search criteria.";
	}
	
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>