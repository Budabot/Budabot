<?php

if (preg_match("/^nlprof (.*)$/i", $message, $arr)) {

	$profession = Util::get_profession_name($arr[1]);
	if ($profession == '') {
		$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, or trader";
		$chatBot->send($msg, $sendto);
		return;
	}

	$sql = "SELECT * FROM nanolines WHERE profession LIKE '$profession' ORDER BY name ASC";
	$db->query($sql);
	$data = $db->fObject('all');

	$header = "$profession Nanolines";
	$blob = Text::make_header($header, array('Help' => '/tell <myname> help nanolines'));
	forEach ($data as $row) {
		if (Setting::get("shownanolineicons") == "1") {
			$blob .= "<img src='rdb://$row->image_id'>\n";
		}
		$blob .= Text::make_chatcmd("$row->name", "/tell <myname> <symbol>nlline $row->id");
		$blob .= "\n";
	}
	$blob .= "\n\nAO Nanos by Voriuste";
	$blob .= "\nModule created by Tyrence (RK2)";
	$msg = Text::make_blob($header, $blob);

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
