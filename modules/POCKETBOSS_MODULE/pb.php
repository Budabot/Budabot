<?php

if (preg_match("/^pb (.+)$/i", $message, $arr)) {
	$search = str_replace(" ", "%", $arr[1]);
	$data = $db->query("SELECT * FROM pbdb WHERE `pb` LIKE ? GROUP BY `pb` ORDER BY `pb`", '%' . $search . '%');
	$numrows = count($data);
	if ($numrows >= 1 && $numrows <= 5) {
		$msg = "Pocketbosses matching: ";
		forEach ($data as $row) {
			$blob = "<highlight>Location:<end> $row->pb_location\n";
			$blob .= "<highlight>Found on:<end> $row->bp_mob\n";
			$blob .= "<highlight>Mob Level:<end> $row->bp_lvl\n";
			$blob .= "<highlight>General Location:<end> $row->bp_location\n";
			$blob .= "_____________________________\n";
			$data2 = $db->query("SELECT * FROM pbdb WHERE pb = ? ORDER BY ql", $row->pb);
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

    $sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
