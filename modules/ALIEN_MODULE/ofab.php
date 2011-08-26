<?php

if (preg_match("/^ofab$/i", $message, $arr)) {

	$db->query("SELECT DISTINCT `type`, `profession` FROM ofab ORDER BY profession ASC");
	$data = $db->fObject('all');
	$blob = "<header> :::::: Ofab Armor Bio-Material Types :::::: <end>\n\n";
	forEach ($data as $row) {
		$profession_link = Text::make_chatcmd($row->profession, "/tell <myname> ofab $row->profession");
		$blob .= "{$profession_link} - Type {$row->type}\n\n";
	}
	$blob .= "\nInfo provided by Wolfbiter (RK1)";

	$msg = Text::make_blob("Ofab Armor Bio-Material Types", $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^ofab (.+)$/i", $message, $arr)) {
	$profession = Util::get_profession_name($arr[1]);
	if ($profession == '') {
		$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, or trader";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$db->query("SELECT * FROM ofab WHERE profession = '{$profession}' ORDER BY `name` ASC");
	$data = $db->fObject('all');
	$blob = "<header> :::::: $profession Ofab Armor :::::: <end>\n\n";
	forEach ($data as $row) {
		$blob .=  Text::make_item($row->body, $row->body, 300, $row->name . " Body Armor") . "\n";
		$blob .=  Text::make_item($row->boots, $row->boots, 300, $row->name . " Boots") . "\n";
		$blob .=  Text::make_item($row->gloves, $row->gloves, 300, $row->name . " Gloves") . "\n";
		$blob .=  Text::make_item($row->helmet, $row->helmet, 300, $row->name . " Helmet") . "\n";
		$blob .=  Text::make_item($row->pants, $row->pants, 300, $row->name . " Pants") . "\n";
		$blob .=  Text::make_item($row->sleeves, $row->sleeves, 300, $row->name . " Sleeves") . "\n\n";
	}
	$blob .= "\nInfo provided by Wolfbiter (RK1)";
	
	$msg = Text::make_blob("$profession Ofab Armor", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>