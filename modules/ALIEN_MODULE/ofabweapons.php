<?php

if (!function_exists('makeAlienWeapon')) {
	function makeAlienWeapon($ql, $name) {
		$db = DB::get_instance();
	
		$name = str_replace("'", "''", $name);
		$db->query("SELECT * FROM aodb WHERE name = '{$name}' AND lowql <= $ql AND highql >= $ql");
		$data = $db->fObject('all');
		$row = $data[0];
		
		return Text::make_item($row->lowid, $row->highid, $ql, $row->name);
	}
}

if (preg_match("/^ofabweapons$/i", $message, $arr)) {
	$db->query("SELECT DISTINCT ql FROM ofabweaponscost ORDER BY ql ASC");
	$qls = $db->fObject('all');

	$db->query("SELECT `type`, `name` FROM ofabweapons ORDER BY name ASC");
	$data = $db->fObject('all');
	$blob = "<header> :::::: Ofab Weapons :::::: <end>\n\n";
	forEach ($data as $row) {
		$blob .= "<pagebreak>{$row->name} - Type {$row->type}\n";
		forEach ($qls as $ql) {
			$ql_link = Text::make_chatcmd($ql->ql, "/tell <myname> ofabweapons {$row->name} {$ql->ql}");
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

	$db->query("SELECT `type`, `vp` FROM ofabweapons w, ofabweaponscost c WHERE w.name = '{$weapon}' AND c.ql = $ql");
	$data = $db->fObject('all');
	if (count($data) == 0) {
		$syntax_error = true;
		return;
	}

	$row = $data[0];
	
	$blob = "<header> :::::: Ofab $weapon (ql $ql) :::::: <end>\n\n";
	$typeQl = round(.8 * $ql);
	$typeLink = Text::make_chatcmd("Kyr'Ozch Bio-Material - Type {$row->type}", "/tell <myname> bioinfo {$row->type} {$typeQl}");
	$blob .= "Upgrade with $typeLink (minimum ql {$typeQl})\n\n";
	for ($i = 1; $i <= 6; $i++) {
		$blob .=  makeAlienWeapon($ql, "Ofab {$weapon} Mk {$i}");
		if ($i == 1) {
			$blob .= "  (<highlight>{$row->vp}<end> VP)";
		}
		$blob .= "\n";
	}

	$msg = Text::make_blob("Ofab $weapon (ql $ql)", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>