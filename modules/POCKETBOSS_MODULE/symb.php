<?php

if (preg_match("/^symb ([a-z]+)$/i", $message, $arr) || preg_match("/^symb ([a-z]+) ([a-z]+)$/i", $message, $arr)) {
	$paramCount = count($arr) - 1;

	$slot = '%';
	$symbtype = '%';

	for ($i = 1; $i <= $paramCount; $i++) {
		switch (strtolower($arr[$i])) {
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
				if (preg_match("/^a/i", $arr[$i])) {
					$symbtype = "Artillery";
				} else if (preg_match("/^s/i", $arr[$i])) {
					$symbtype = "Support";
				} else if (preg_match("/^i/i", $arr[$i])) {
					$symbtype = "Infantry";
				} else if (preg_match("/^e/i", $arr[$i])) {
					$symbtype = "Extermination";
				} else if (preg_match("/^c/i", $arr[$i])) {
					$symbtype = "Control";
				}
		}
	}

	$data = $db->query("SELECT * FROM pbdb WHERE `slot` LIKE ? AND `type` LIKE ? ORDER BY `ql` DESC, `type` ASC", $slot, $symbtype);
	$numrows = count($data);
	if ($numrows != 0) {
		$blob = '';
		forEach ($data as $row) {
			$name = "QL $row->ql $row->line $row->slot Symbiant, $row->type Unit Aban";
			$blob .= "<pagebreak>" . Text::make_item($row->itemid, $row->itemid, $row->ql, $name)."\n";
			$blob .= "Found on ".Text::make_chatcmd($row->pb, "/tell <myname> pb $row->pb");
			$blob .= "\n\n";
		}
		$msg = Text::make_blob("Symbiant Search Results ($numrows)", $blob);
	} else {
		$msg = "Could not find any symbiants that matched your search criteria.";
	}

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
