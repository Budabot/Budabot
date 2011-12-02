<?php

if (!function_exists('makeAlienWeapon')) {
	function makeAlienWeapon($ql, $name) {
		$db = DB::get_instance();
	
		$name = str_replace("'", "''", $name);
		$data = $db->query("SELECT * FROM aodb WHERE name = '{$name}' AND lowql <= $ql AND highql >= $ql");
		$row = $data[0];
		
		return Text::make_item($row->lowid, $row->highid, $ql, $row->name);
	}
}

if (preg_match("/^ofabweapons$/i", $message, $arr)) {
	$qls = $db->query("SELECT DISTINCT ql FROM ofabweaponscost ORDER BY ql ASC");

	$data = $db->query("SELECT `type`, `name` FROM ofabweapons ORDER BY name ASC");
	$blob = "<header> :::::: Ofab Weapons :::::: <end>\n\n";
	forEach ($data as $row) {
		$blob .= "<pagebreak>{$row->name} - Type {$row->type}\n";
		forEach ($qls as $row2) {
			$ql_link = Text::make_chatcmd($row2->ql, "/tell <myname> ofabweapons {$row->name} {$row2->ql}");
			$blob .= "[{$ql_link}] ";
		}
		$blob .= "\n\n";
	}

	$msg = Text::make_blob("Ofab Weapons", $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^ofabweapons (.+) (\\d+)$/i", $message, $arr) || preg_match("/^ofabweapons (.+)$/i", $message, $arr)) {
	if ($arr[2]) {
		$ql = $arr[2];
	} else {
		$ql = 300;
	}

	$weapon = ucfirst($arr[1]);

	$data = $db->query("SELECT `type`, `vp` FROM ofabweapons w, ofabweaponscost c WHERE w.name = '{$weapon}' AND c.ql = $ql");
	if (count($data) == 0) {
		$syntax_error = true;
		return;
	}

	$row = $data[0];
	
	$blob = "<header> :::::: Ofab $weapon (QL $ql) :::::: <end>\n\n";
	$typeQl = round(.8 * $ql);
	$typeLink = Text::make_chatcmd("Kyr'Ozch Bio-Material - Type {$row->type}", "/tell <myname> bioinfo {$row->type} {$typeQl}");
	$blob .= "Upgrade with $typeLink (minimum QL {$typeQl})\n\n";
	
	$qls = $db->query("SELECT DISTINCT ql FROM ofabweaponscost ORDER BY ql ASC");
	forEach ($qls as $row2) {
		if ($row2->ql == $ql) {
			$blob .= "[{$row2->ql}] ";
		} else {
			$ql_link = Text::make_chatcmd($row2->ql, "/tell <myname> ofabweapons {$weapon} {$row2->ql}");
			$blob .= "[{$ql_link}] ";
		}
	}
	$blob .= "\n\n";

	for ($i = 1; $i <= 6; $i++) {
		$blob .=  makeAlienWeapon($ql, "Ofab {$weapon} Mk {$i}");
		if ($i == 1) {
			$blob .= "  (<highlight>{$row->vp}<end> VP)";
		}
		$blob .= "\n";
	}

	$msg = Text::make_blob("Ofab $weapon (QL $ql)", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>