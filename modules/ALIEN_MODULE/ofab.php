<?php

if (preg_match("/^ofab$/i", $message, $arr)) {
	$db->query("SELECT DISTINCT ql FROM ofabarmorcost ORDER BY ql ASC");
	$qls = $db->fObject('all');

	$db->query("SELECT `type`, `profession` FROM ofabarmortype ORDER BY profession ASC");
	$data = $db->fObject('all');
	$blob = "<header> :::::: Ofab Armor Bio-Material Types :::::: <end>\n\n";
	forEach ($data as $row) {
		$blob .= "{$row->profession} - Type {$row->type}\n";
		forEach ($qls as $ql) {
			$ql_link = Text::make_chatcmd($ql->ql, "/tell <myname> ofab {$row->profession} {$ql->ql}");
			$blob .= "[{$ql_link}] ";
		}
		$blob .= "\n\n";
	}
	$blob .= "\nInfo provided by Wolfbiter (RK1), Mdkdoc240 (RK2)";

	$msg = Text::make_blob("Ofab Armor Bio-Material Types", $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^ofab (.+) (\\d+)$/i", $message, $arr) || preg_match("/^ofab (.+)$/i", $message, $arr)) {
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
	
	$db->query("SELECT * FROM ofabarmor o1 JOIN ofabarmorcost o2 ON o1.slot = o2.slot WHERE o1.profession = '{$profession}' AND o2.ql = {$ql} ORDER BY upgrade ASC, slot ASC");
	$data = $db->fObject('all');
	$blob = "<header> :::::: $profession Ofab Armor :::::: <end>\n\n";
	forEach ($data as $row) {
		$blob .=  Text::make_item($row->lowid, $row->highid, $ql, $row->name) . "\n";
	}
	$blob .= "\nInfo provided by Wolfbiter (RK1), Mdkdoc240 (RK2)";
	
	$msg = Text::make_blob("$profession Ofab Armor", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>