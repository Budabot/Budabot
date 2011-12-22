<?php

if (preg_match("/^ofabarmor$/i", $message, $arr)) {
	$qls = $db->query("SELECT DISTINCT ql FROM ofabarmorcost ORDER BY ql ASC");

	$data = $db->query("SELECT `type`, `profession` FROM ofabarmortype ORDER BY profession ASC");
	$blob = "<header> :::::: Ofab Armor Bio-Material Types :::::: <end>\n\n";
	forEach ($data as $row) {
		$blob .= "<pagebreak>{$row->profession} - Type {$row->type}\n";
		forEach ($qls as $row2) {
			$ql_link = Text::make_chatcmd($row2->ql, "/tell <myname> ofabarmor {$row->profession} {$row2->ql}");
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

	$typelist = $db->query("SELECT type FROM ofabarmortype WHERE profession = ?", $profession);
	$type = $typelist[0]->type;
	
	$data = $db->query("SELECT * FROM ofabarmor o1 LEFT JOIN ofabarmorcost o2 ON o1.slot = o2.slot WHERE o1.profession = ? AND o2.ql = ? ORDER BY upgrade ASC, name ASC", $profession, $ql);
	if (count($data) == 0) {
		$syntax_error = true;
		return;
	}
	
	$blob = "<header> :::::: $profession Ofab Armor (QL $ql) :::::: <end>\n\n";
	$typeLink = Text::make_chatcmd("Kyr'Ozch Bio-Material - Type {$type}", "/tell <myname> bioinfo {$type}");
	$typeQl = round(.8 * $ql);
	$blob .= "Upgrade with $typeLink (minimum QL {$typeQl})\n\n";
	
	$qls = $db->query("SELECT DISTINCT ql FROM ofabarmorcost ORDER BY ql ASC");
	forEach ($qls as $row2) {
		if ($row2->ql == $ql) {
			$blob .= "[{$row2->ql}] ";
		} else {
			$ql_link = Text::make_chatcmd($row2->ql, "/tell <myname> ofabarmor {$profession} {$row2->ql}");
			$blob .= "[{$ql_link}] ";
		}
	}
	$blob .= "\n";
	
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