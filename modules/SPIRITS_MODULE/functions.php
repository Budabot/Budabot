<?php

function formatSpiritOutput($data) {
	$chatBot = Registry::getInstance('chatBot');
	$db = Registry::getInstance('db');
	
	if (count($data) == 0) {
		return "No matches found.";
	}

	$msg = '';
	forEach ($data as $row) {
		$slot = $row->spot;
		$lvl = $row->level;
		$lowid = $row->id;
		$agi = $row->agility;
		
		$data2 = $db->query("SELECT * FROM aodb WHERE lowid = ?", $lowid);
		forEach ($data2 as $row); {
			$highid = $row->highid;
			$icon = $row->icon;
			$name = $row->name;
			$ql = $row->highql;
		}
		$msg .= Text::make_image($icon) . ' ';
		$msg .= Text::make_item($lowid, $highid, $ql, $name) . "\n";
		$msg .= "<green>Minimum Level=$lvl   Slot=$slot   Agility/Sense Needed=$agi<end>\n\n";
	}
	return $msg;
}

?>