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
	$count = $db->numrows();

	forEach ($data as $row) {
		if ($chatBot->settings["shownanolineicons"] == "1") {
			$window .= "<img src='rdb://$row->image_id'>\n";
		}
		$window .= Text::make_chatcmd("$row->name", "/tell <myname> <symbol>nlline $row->id");
		$window .= "\n";
	}

	$msg = '';
	if ($count > 0) {
		$window = Text::make_header("$profession Nanolines", "none") . $window;
		$window .= "\n\nAO Nanos by Voriuste";
		$window .= "\nModule created by Tyrence (RK2)";
		$msg = Text::make_blob("$profession Nanolines", $window);
	} else {
		$msg = "Profession not found.";
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
