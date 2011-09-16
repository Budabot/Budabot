<?php

if (preg_match("/^ofabarmor$/i", $message, $arr)) {
	$db->query("SELECT DISTINCT ql FROM ofabarmorcost ORDER BY ql ASC");
	$qls = $db->fObject('all');

	$db->query("SELECT `type`, `profession` FROM ofabarmortype ORDER BY profession ASC");
	$data = $db->fObject('all');
	$blob = "<header> :::::: Ofab Armor Bio-Material Types :::::: <end>\n\n";
	forEach ($data as $row) {
		$blob .= "<pagebreak>{$row->profession} - Type {$row->type}\n";
		forEach ($qls as $ql) {
			$ql_link = Text::make_chatcmd($ql->ql, "/tell <myname> ofabarmor {$row->profession} {$ql->ql}");
			$blob .= "[{$ql_link}] ";
		}
		$blob .= "\n\n";
	}

	$msg = Text::make_blob("Ofab Armor Bio-Material Types", $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^ofabarmor (.+) (\\d+)$/i", $message, $arr) || preg_match("/^ofabarmor (.+)$/i", $message, $arr)) {
	if ($arr[2]) {
		$ql = $arr[2];
	} else {
		$ql = 300;
	}

	$profession = Util::get_profession_name($arr[1]);
	if ($profession == '') {
		$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, or trader";
		$chatBot->send($msg, $sendto);
		return;
	}

	$db->query("SELECT type FROM ofabarmortype WHERE profession = '$profession'");
	$typelist = $db->fObject('all');
	$type = $typelist[0]->type;
	
	$db->query("SELECT * FROM ofabarmor o1 LEFT JOIN ofabarmorcost o2 ON o1.slot = o2.slot WHERE o1.profession = '{$profession}' AND o2.ql = {$ql} ORDER BY upgrade ASC, name ASC");
	$data = $db->fObject('all');
	if (count($data) == 0) {
		$syntax_error = true;
		return;
	}
	
	$blob = "<header> :::::: $profession Ofab Armor (QL $ql) :::::: <end>\n\n";
	$typeLink = Text::make_chatcmd("Kyr'Ozch Bio-Material - Type {$type}", "/tell <myname> bioinfo {$type}");
	$typeQl = round(.8 * $ql);
	$blob .= "Upgrade with $typeLink (minimum QL {$typeQl})\n";
	$current_upgrade = $row->upgrade;
	forEach ($data as $row) {
		if ($current_upgrade != $row->upgrade) {
			$current_upgrade = $row->upgrade;
			$blob .= "\n";
		}
		$blob .=  Text::make_item($row->lowid, $row->highid, $ql, $row->name);

		if ($row->upgrade == 0 || $row->upgrade == 3) {
			$blob .= "  (<highlight>$row->vp<end> VP)";
			$total_vp = $total_vp + $row->vp;
		}
		$blob .= "\n";
	}
	$blob .= "\nVP Cost for full set: <highlight>$total_vp<end>";
	
	$msg = Text::make_blob("$profession Ofab Armor (QL $ql)", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>